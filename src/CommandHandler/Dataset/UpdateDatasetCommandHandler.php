<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\UpdateDatasetCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateDatasetCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(UpdateDatasetCommand $command): void
    {
        $dataset = $command->getDataset();

        if (! $this->security->isGranted('edit', $dataset)) {
            throw new NoAccessPermission();
        }

        $slug = $command->getSlug();

        $dataset->setSlug($slug);
        $dataset->setIsPublished($command->getPublished());

        $this->em->persist($dataset);
        $this->em->flush();
    }
}
