<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelVersionCommand;
use App\CommandHandler\DataSpecification\Common\DataSpecificationVersionCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroupOption;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\DataSpecification\MetadataModel\Predicate;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Entity\Enum\VersionType;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class CreateMetadataModelVersionCommandHandler extends DataSpecificationVersionCommandHandler
{
    public function __invoke(CreateMetadataModelVersionCommand $command): MetadataModelVersion
    {
        $metadataModel = $command->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $latestVersion = $metadataModel->getLatestVersion();
        assert($latestVersion instanceof MetadataModelVersion);

        $newVersion = $this->duplicateVersion($latestVersion, $command->getVersionType());

        $metadataModel->addVersion($newVersion);

        $this->em->persist($newVersion);
        $this->em->persist($metadataModel);

        $this->em->flush();

        return $newVersion;
    }

    private function duplicateVersion(MetadataModelVersion $latestVersion, VersionType $versionType): MetadataModelVersion
    {
        $versionNumber = $this->versionNumberHelper->getNewVersion($latestVersion->getVersion(), $versionType);

        $newVersion = new MetadataModelVersion($versionNumber);

        // Add prefixes
        foreach ($latestVersion->getPrefixes() as $prefix) {
            $newVersion->addPrefix(new NamespacePrefix($prefix->getPrefix(), $prefix->getUri()));
        }

        // Add option groups
        foreach ($latestVersion->getOptionGroups() as $optionGroup) {
            $newOptionGroup = new MetadataModelOptionGroup($newVersion, $optionGroup->getTitle(), $optionGroup->getDescription());

            foreach ($optionGroup->getOptions() as $option) {
                $newOption = new MetadataModelOptionGroupOption(
                    $option->getTitle(),
                    $option->getDescription(),
                    $option->getValue(),
                    $option->getOrder()
                );

                $newOptionGroup->addOption($newOption);
            }

            $newVersion->addOptionGroup($optionGroup);
        }

        // Add nodes

        /** @var ArrayCollection<Node> $nodes */
        $nodes = new ArrayCollection();

        foreach ($latestVersion->getElements() as $node) {
            if ($node instanceof RecordNode) {
                $newNode = new RecordNode($newVersion, $node->getResourceType());
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

                if (! $node->isAnnotatedValue()) {
                    $newNode->setDataType($node->getDataType());
                }
            } elseif ($node instanceof ChildrenNode) {
                $newNode = new ChildrenNode($newVersion, $node->getResourceType());
            } elseif ($node instanceof ParentsNode) {
                $newNode = new ParentsNode($newVersion, $node->getResourceType());
            } else {
                throw new InvalidNodeType();
            }

            $nodes->set($node->getId(), $newNode);
            $newVersion->addElement($newNode);
        }

        // Add predicates
        /** @var ArrayCollection<Predicate> $predicates */
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
            /** @var MetadataModelGroup $module */
            $newModule = new MetadataModelGroup(
                $module->getTitle(),
                $module->getOrder(),
                $module->getResourceType(),
                $newVersion
            );

            foreach ($module->getElementGroups() as $triple) {
                assert($triple instanceof Triple);

                $node = $nodes->get($triple->getSubject()->getId());
                assert($node instanceof Node);

                $predicate = $predicates->get($triple->getPredicate()->getId());
                assert($predicate instanceof Predicate);

                $object = $nodes->get($triple->getObject()->getId());
                assert($object instanceof Node);

                $newTriple = new Triple(
                    $newModule,
                    $node,
                    $predicate,
                    $object
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
