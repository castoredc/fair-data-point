<?php
declare(strict_types=1);

namespace App\CommandHandler\Catalog;

use App\Command\Catalog\UpdateCatalogCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateCatalogCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(UpdateCatalogCommand $command): void
    {
        $catalog = $command->getCatalog();

        if (! $this->security->isGranted('edit', $catalog)) {
            throw new NoAccessPermission();
        }

        $slug = $command->getSlug();

        $catalog->setSlug($slug);
        $catalog->setAcceptSubmissions($command->isAcceptSubmissions());

        $submissionsAccessesData = $command->isAcceptSubmissions() ? $command->isSubmissionAccessesData() : false;
        $catalog->setSubmissionAccessesData($submissionsAccessesData);

        $this->em->persist($catalog);
        $this->em->flush();
    }
}
