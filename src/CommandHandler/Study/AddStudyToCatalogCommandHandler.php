<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\AddStudyToCatalogCommand;
use App\Exception\CatalogNotExceptingSubmissions;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddStudyToCatalogCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(AddStudyToCatalogCommand $command): void
    {
        if (! $command->getCatalog()->isAcceptingSubmissions() && ! $this->security->isGranted('ROLE_ADMIN')) {
            throw new CatalogNotExceptingSubmissions();
        }

        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermission();
        }

        $command->getCatalog()->addStudy($command->getStudy());
        $this->em->persist($command->getCatalog());

        $this->em->flush();
    }
}
