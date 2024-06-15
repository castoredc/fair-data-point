<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\Enum\ResourceType;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMetadataModelCommandHandler
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(CreateMetadataModelCommand $command): MetadataModel
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $metadataModel = new MetadataModel($command->getTitle(), $command->getDescription());

        $version = new MetadataModelVersion(new Version(self::DEFAULT_VERSION_NUMBER));

        $fdpRecordNode = new RecordNode($version, ResourceType::fdp());
        $catalogRecordNode = new RecordNode($version, ResourceType::catalog());
        $datasetRecordNode = new RecordNode($version, ResourceType::dataset());
        $distributionRecordNode = new RecordNode($version, ResourceType::distribution());
        $studyRecordNode = new RecordNode($version, ResourceType::study());

        $fdpChildrenNode = new ChildrenNode($version, ResourceType::fdp());
        $catalogChildrenNode = new ChildrenNode($version, ResourceType::catalog());
        $datasetChildrenNode = new ChildrenNode($version, ResourceType::dataset());
        $distributionChildrenNode = new ChildrenNode($version, ResourceType::distribution());

        $fdpParentsNode = new ParentsNode($version, ResourceType::fdp());
        $catalogParentsNode = new ParentsNode($version, ResourceType::catalog());
        $datasetParentsNode = new ParentsNode($version, ResourceType::dataset());
        $distributionParentsNode = new ParentsNode($version, ResourceType::distribution());

        $metadataModel->addVersion($version);

        $version->addNode($fdpRecordNode);
        $version->addNode($catalogRecordNode);
        $version->addNode($datasetRecordNode);
        $version->addNode($distributionRecordNode);
        $version->addNode($studyRecordNode);

        $version->addNode($fdpChildrenNode);
        $version->addNode($catalogChildrenNode);
        $version->addNode($datasetChildrenNode);
        $version->addNode($distributionChildrenNode);

        $version->addNode($fdpParentsNode);
        $version->addNode($catalogParentsNode);
        $version->addNode($datasetParentsNode);
        $version->addNode($distributionParentsNode);

        $this->em->persist($metadataModel);
        $this->em->persist($version);

        $this->em->persist($fdpRecordNode);
        $this->em->persist($catalogRecordNode);
        $this->em->persist($datasetRecordNode);
        $this->em->persist($distributionRecordNode);

        $this->em->persist($fdpChildrenNode);
        $this->em->persist($catalogChildrenNode);
        $this->em->persist($datasetChildrenNode);
        $this->em->persist($distributionChildrenNode);

        $this->em->persist($fdpParentsNode);
        $this->em->persist($catalogParentsNode);
        $this->em->persist($datasetParentsNode);
        $this->em->persist($distributionParentsNode);

        $this->em->flush();

        return $metadataModel;
    }
}
