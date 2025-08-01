<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\UpdateDatasetCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DatasetVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class UpdateDatasetCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(UpdateDatasetCommand $command): void
    {
        $dataset = $command->getDataset();

        if (! $this->security->isGranted(DatasetVoter::EDIT, $dataset)) {
            throw new NoAccessPermission();
        }

        $defaultMetadataModel = $this->em->getRepository(MetadataModel::class)->find($command->getDefaultMetadataModelId());
        assert($defaultMetadataModel instanceof MetadataModel);

        $slug = $command->getSlug();

        $dataset->setSlug($slug);
        $dataset->setIsPublished($command->getPublished());
        $dataset->setDefaultMetadataModel($defaultMetadataModel);

        $this->em->persist($dataset);
        $this->em->flush();
    }
}
