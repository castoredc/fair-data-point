<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\GetPaginatedStudiesCommand;
use App\Entity\PaginatedResultCollection;
use App\Entity\Study;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetPaginatedStudiesCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(GetPaginatedStudiesCommand $command): PaginatedResultCollection
    {
        $studyRepository = $this->em->getRepository(Study::class);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $count = $studyRepository->countStudies(
            $command->getCatalog(),
            $command->getAgent(),
            $command->getHideCatalogs(),
            $command->getIncludeUnpublished(),
            $isAdmin
        );

        $studies = $studyRepository->findStudies(
            $command->getCatalog(),
            $command->getAgent(),
            $command->getHideCatalogs(),
            $command->getIncludeUnpublished(),
            $command->getPerPage(),
            $command->getPage(),
            $isAdmin
        );

        return new PaginatedResultCollection(
            $studies,
            $command->getPage(),
            $command->getPerPage(),
            $count
        );
    }
}
