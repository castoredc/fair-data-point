<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Exception\NotFound;
use App\Service\VersionNumberHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
abstract class CreateMetadataCommandHandler
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

    public function __construct(protected EntityManagerInterface $em, protected Security $security, protected VersionNumberHelper $versionNumberHelper)
    {
    }

    protected function getMetadataModelVersion(string $modelId, string $versionId): MetadataModelVersion
    {
        $model = $this->em->getRepository(MetadataModel::class)->find($modelId);
        $version = $this->em->getRepository(MetadataModelVersion::class)->find($versionId);

        if ($model === null || $version === null || $version->getMetadataModel() !== $model) {
            throw new NotFound();
        }

        return $version;
    }
}
