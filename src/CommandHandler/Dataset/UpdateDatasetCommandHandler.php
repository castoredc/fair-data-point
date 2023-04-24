<?php
declare(strict_types=1);

namespace App\CommandHandler\Dataset;

use App\Command\Dataset\UpdateDatasetCommand;
use App\Entity\FAIRData\Dataset;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function uniqid;

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

        $slug = $command->getSlug();

        // Check for duplicate slugs
        if ($this->em->getRepository(Dataset::class)->findBySlug($slug) !== null) {
            $slug .= '-' . uniqid();
        }

        $dataset->setSlug($slug);
        $dataset->setIsPublished($command->getPublished());

        $this->em->persist($dataset);
        $this->em->flush();
    }
}
