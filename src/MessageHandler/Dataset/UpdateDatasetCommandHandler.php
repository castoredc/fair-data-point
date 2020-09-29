<?php
declare(strict_types=1);

namespace App\MessageHandler\Dataset;

use App\Exception\NoAccessPermission;
use App\Message\Dataset\UpdateDatasetCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateDatasetCommandHandler implements MessageHandlerInterface
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

        $dataset->setSlug($command->getSlug());
        $dataset->setIsPublished($command->getPublished());

        $this->em->persist($dataset);
        $this->em->flush();
    }
}
