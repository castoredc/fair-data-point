<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Predicate;
use App\Entity\Data\DataModel\Triple;
use App\Entity\Enum\VersionType;
use App\Exception\InvalidNodeType;
use App\Exception\NoAccessPermission;
use App\Message\Data\CreateDataModelVersionCommand;
use App\Service\VersionNumberHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDataModelVersionCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    /** @var VersionNumberHelper */
    private $versionNumberHelper;

    public function __construct(EntityManagerInterface $em, Security $security, VersionNumberHelper $versionNumberHelper)
    {
        $this->em = $em;
        $this->security = $security;
        $this->versionNumberHelper = $versionNumberHelper;
    }

    public function __invoke(CreateDataModelVersionCommand $command): DataModelVersion
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();
        $latestVersion = $dataModel->getLatestVersion();

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
            /** @var NamespacePrefix $prefix */
            $newVersion->addPrefix(new NamespacePrefix($prefix->getPrefix(), $prefix->getUri()));
        }

        // Add nodes
        $nodes = new ArrayCollection();

        foreach ($latestVersion->getNodes() as $node) {
            /** @var Node $node */
            if ($node instanceof RecordNode) {
                $newNode = new RecordNode($newVersion);
            } elseif ($node instanceof InternalIriNode) {
                $newNode = new InternalIriNode($newVersion, $node->getTitle(), $node->getDescription());
                $newNode->setSlug($node->getSlug());
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
                    $newNode->setIsRepeated($node->isRepeated());
                }
            } else {
                throw new InvalidNodeType();
            }

            $nodes->set($node->getId(), $newNode);
            $newVersion->addNode($newNode);
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
        foreach ($latestVersion->getModules() as $module) {
            /** @var DataModelModule $module */
            $newModule = new DataModelModule($module->getTitle(), $module->getOrder(), $module->isRepeated(), $newVersion);

            foreach ($module->getTriples() as $triple) {
                /** @var Triple $triple */
                $newTriple = new Triple(
                    $newModule,
                    $nodes->get($triple->getSubject()->getId()),
                    $predicates->get($triple->getPredicate()->getId()),
                    $nodes->get($triple->getObject()->getId())
                );

                $newModule->addTriple($newTriple);
            }

            $newVersion->addModule($newModule);
        }

        return $newVersion;
    }
}
