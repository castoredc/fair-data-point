<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Exception\CatalogNotExceptingSubmissions;
use App\Exception\NoAccessPermission;
use App\Command\Study\AddStudyToCatalogCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddStudyToCatalogCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
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
