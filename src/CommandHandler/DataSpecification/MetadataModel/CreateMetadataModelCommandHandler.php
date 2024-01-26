<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMetadataModelCommandHandler
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateMetadataModelCommand $command): MetadataModel
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $metadataModel = new MetadataModel($command->getTitle(), $command->getDescription());

        $version = new MetadataModelVersion(new Version(self::DEFAULT_VERSION_NUMBER));
        $metadataModel->addVersion($version);

        $recordNode = new RecordNode($version);
        $version->addNode($recordNode);

        $this->em->persist($metadataModel);
        $this->em->persist($version);
        $this->em->persist($recordNode);
        $this->em->flush();

        return $metadataModel;
    }
}
