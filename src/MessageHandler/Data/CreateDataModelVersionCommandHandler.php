<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Api\Resource\Data\DataModelVersionApiResource;
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
use DeepCopy\DeepCopy;
use DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use DeepCopy\Filter\Doctrine\DoctrineEmptyCollectionFilter;
use DeepCopy\Filter\Doctrine\DoctrineProxyFilter;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\Doctrine\DoctrineProxyMatcher;
use DeepCopy\Matcher\PropertyMatcher;
use DeepCopy\Matcher\PropertyNameMatcher;
use DeepCopy\Matcher\PropertyTypeMatcher;
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

        $newVersion = $this->duplicateVersion($latestVersion);
        $versionNumber = $this->versionNumberHelper->getNewVersion($latestVersion->getVersion(), $command->getVersionType());
        $newVersion->setVersion($versionNumber);

        // $newVersion = $this->duplicateVersion($latestVersion, $command->getVersionType());

        $dataModel->addVersion($newVersion);

        // dump($newVersion);
        // dump($dataModel->getVersions());
        //
        // dump((new DataModelVersionApiResource($latestVersion))->toArray());
        // dump((new DataModelVersionApiResource($newVersion))->toArray());
        // die();

        $this->em->persist($newVersion);
        $this->em->persist($dataModel);

        $this->em->flush();

        return $newVersion;
    }

    /**
     * @return DataModelVersion
     */
    private function duplicateVersion(DataModelVersion $latestVersion): DataModelVersion
    {
        $deepCopy = new DeepCopy();
        $deepCopy->addFilter(new DoctrineProxyFilter(), new DoctrineProxyMatcher());

        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelVersion::class, 'id'));
        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelVersion::class, 'dataModel'));
        $deepCopy->addFilter(new DoctrineEmptyCollectionFilter(), new PropertyMatcher(DataModelVersion::class, 'distributions'));

        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelVersion::class, 'createdAt'));
        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelVersion::class, 'createdBy'));
        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelVersion::class, 'updatedAt'));
        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelVersion::class, 'updatedBy'));
        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelVersion::class, 'version'));

        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelModule::class, 'id'));
        // $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(DataModelModule::class, 'dataModel'));

        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(NamespacePrefix::class, 'id'));
        // $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(NamespacePrefix::class, 'dataModel'));

        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(Node::class, 'id'));
        // $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(Node::class, 'dataModel'));

        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(Predicate::class, 'id'));
        // $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(Predicate::class, 'dataModel'));

        $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(Triple::class, 'id'));
        // $deepCopy->addFilter(new SetNullFilter(), new PropertyMatcher(Triple::class, 'dataModel'));

        $deepCopy->addFilter(new DoctrineCollectionFilter(), new PropertyTypeMatcher('Doctrine\Common\Collections\Collection'));

        $deepCopy->addFilter(new KeepFilter(), new PropertyNameMatcher('createdBy'));
        $deepCopy->addFilter(new KeepFilter(), new PropertyNameMatcher('updatedBy'));

        /** @var DataModelVersion $newVersion */
        $newVersion = $deepCopy->copy($latestVersion);

        return $newVersion;
    }

    // /**
    //  * @param DataModelVersion $latestVersion
    //  *
    //  * @return DataModelVersion
    //  */
    // private function duplicateVersion(DataModelVersion $latestVersion, VersionType $versionType): DataModelVersion
    // {
    //     $versionNumber = $this->versionNumberHelper->getNewVersion($latestVersion->getVersion(), $versionType);
    //
    //     $newVersion = new DataModelVersion($versionNumber);
    //
    //     // Add prefixes
    //     foreach($latestVersion->getPrefixes() as $prefix) {
    //         /** @var NamespacePrefix $prefix */
    //         $newVersion->addPrefix(new NamespacePrefix($prefix->getPrefix(), $prefix->getUri()));
    //     }
    //
    //     // Add nodes
    //     $nodes = new ArrayCollection();
    //
    //     foreach($latestVersion->getNodes() as $node) {
    //         /** @var Node $node */
    //         if ($node instanceof RecordNode) {
    //             $newNode = new RecordNode($newVersion);
    //         } elseif ($node instanceof InternalIriNode) {
    //             $newNode = new InternalIriNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setSlug($node->getSlug());
    //         } elseif ($node instanceof ExternalIriNode) {
    //             $newNode = new ExternalIriNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setIri($node->getIri());
    //         } elseif ($node instanceof LiteralNode) {
    //             $newNode = new LiteralNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setValue($node->getValue());
    //             $newNode->setDataType($node->getDataType());
    //         } elseif ($node instanceof ValueNode) {
    //             $newNode = new ValueNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setIsAnnotatedValue($node->isAnnotatedValue());
    //
    //             if (! $node->isAnnotatedValue()) {
    //                 $newNode->setDataType($node->getDataType());
    //             }
    //         } else {
    //             throw new InvalidNodeType();
    //         }
    //
    //         $nodes->set($node->getId(), $newNode);
    //         $newVersion->addNode($newNode);
    //     }
    //
    //     // Add nodes
    //     $predicates = new ArrayCollection();
    //
    //     foreach($latestVersion->getNodes() as $node) {
    //         /** @var Node $node */
    //         if ($node instanceof RecordNode) {
    //             $newNode = new RecordNode($newVersion);
    //         } elseif ($node instanceof InternalIriNode) {
    //             $newNode = new InternalIriNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setSlug($node->getSlug());
    //         } elseif ($node instanceof ExternalIriNode) {
    //             $newNode = new ExternalIriNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setIri($node->getIri());
    //         } elseif ($node instanceof LiteralNode) {
    //             $newNode = new LiteralNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setValue($node->getValue());
    //             $newNode->setDataType($node->getDataType());
    //         } elseif ($node instanceof ValueNode) {
    //             $newNode = new ValueNode($newVersion, $node->getTitle(), $node->getDescription());
    //             $newNode->setIsAnnotatedValue($node->isAnnotatedValue());
    //
    //             if (! $node->isAnnotatedValue()) {
    //                 $newNode->setDataType($node->getDataType());
    //             }
    //         } else {
    //             throw new InvalidNodeType();
    //         }
    //
    //         $nodes->set($node->getId(), $newNode);
    //         $newVersion->addNode($newNode);
    //     }
    //
    //     return $newVersion;
    // }
}
