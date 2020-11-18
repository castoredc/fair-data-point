<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\CreateDataModelVersionCommand;
use App\CommandHandler\Data\DataSpecificationVersionCommandHandler;
use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Predicate;
use App\Entity\Data\DataModel\Triple;
use App\Entity\Enum\VersionType;
use App\Exception\InvalidNodeType;
use App\Exception\NoAccessPermission;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function assert;

class CreateDataModelVersionCommandHandler extends DataSpecificationVersionCommandHandler implements MessageHandlerInterface
{
    public function __invoke(CreateDataModelVersionCommand $command): DataModelVersion
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();
        $latestVersion = $dataModel->getLatestVersion();
        assert($latestVersion instanceof DataModelVersion);

        $newVersion = $this->duplicateVersion($latestVersion, $command->getVersionType());

        $dataModel->addVersion($newVersion);

        $this->em->persist($newVersion);
        $this->em->persist($dataModel);

        $this->em->flush();

        return $newVersion;
    }

    private function duplicateVersion(DataModelVersion $latestVersion, VersionType $versionType): DataModelVersion
    {
        $versionNumber = $this->versionNumberHelper->getNewVersion($latestVersion->getVersion(), $versionType);

        $newVersion = new DataModelVersion($versionNumber);

        // Add prefixes
        foreach ($latestVersion->getPrefixes() as $prefix) {
            $newVersion->addPrefix(new NamespacePrefix($prefix->getPrefix(), $prefix->getUri()));
        }

        // Add nodes
        $nodes = new ArrayCollection();

        foreach ($latestVersion->getElements() as $node) {
            if ($node instanceof RecordNode) {
                $newNode = new RecordNode($newVersion);
            } elseif ($node instanceof InternalIriNode) {
                $newNode = new InternalIriNode($newVersion, $node->getTitle(), $node->getDescription());
                $newNode->setSlug($node->getSlug());
                $newNode->setIsRepeated($node->isRepeated());
            } elseif ($node instanceof ExternalIriNode) {
                $newNode = new ExternalIriNode($newVersion, $node->getTitle(), $node->getDescription());
                $newNode->setIri($node->getIri());
            } elseif ($node instanceof LiteralNode) {
                $newNode = new LiteralNode($newVersion, $node->getTitle(), $node->getDescription());
                $newNode->setValue($node->getValue());
                $newNode->setDataType($node->getDataType());
            } elseif ($node instanceof ValueNode) {
                $newNode = new ValueNode($newVersion, $node->getTitle(), $node->getDescription());
                $newNode->setIsAnnotatedValue($node->isAnnotatedValue());
                $newNode->setIsRepeated($node->isRepeated());

                if (! $node->isAnnotatedValue()) {
                    $newNode->setDataType($node->getDataType());
                }
            } else {
                throw new InvalidNodeType();
            }

            $nodes->set($node->getId(), $newNode);
            $newVersion->addElement($newNode);
        }

        // Add predicates
        $predicates = new ArrayCollection();

        foreach ($latestVersion->getPredicates() as $predicate) {
            /** @var Predicate $predicate */
            $newPredicate = new Predicate($newVersion, $predicate->getIri());

            $predicates->set($predicate->getId(), $newPredicate);
            $newVersion->addPredicate($newPredicate);
        }

        // Add modules
        $modules = new ArrayCollection();

        foreach ($latestVersion->getGroups() as $module) {
            /** @var DataModelGroup $module */
            $newModule = new DataModelGroup($module->getTitle(), $module->getOrder(), $module->isRepeated(), $module->isDependent(), $newVersion);

            foreach ($module->getElementGroups() as $triple) {
                assert($triple instanceof Triple);

                $newTriple = new Triple(
                    $newModule,
                    $nodes->get($triple->getSubject()->getId()),
                    $predicates->get($triple->getPredicate()->getId()),
                    $nodes->get($triple->getObject()->getId())
                );

                $newModule->addElementGroup($newTriple);
            }

            if ($module->isDependent() && $module->getDependencies() !== null) {
                $dependency = $module->getDependencies();
                $newDependency = $this->duplicateDependencies($dependency, $nodes);

                $newModule->setDependencies($newDependency);
            }

            $modules->add($newModule);
        }

        $newVersion->setGroups($modules);

        return $newVersion;
    }
}
