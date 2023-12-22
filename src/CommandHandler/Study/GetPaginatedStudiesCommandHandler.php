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
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetPaginatedStudiesCommand $command): PaginatedResultCollection
    {
        $datasetRepository = $this->em->getRepository(Study::class);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $count = $datasetRepository->countStudies(
            $command->getCatalog(),
            $command->getAgent(),
            $command->getHideCatalogs(),
            $command->getSearch(),
            $command->getStudyType(),
            $command->getMethodType(),
            $command->getCountry(),
            $isAdmin
        );

        $studies = $datasetRepository->findStudies(
            $command->getCatalog(),
            $command->getAgent(),
            $command->getHideCatalogs(),
            $command->getSearch(),
            $command->getStudyType(),
            $command->getMethodType(),
            $command->getCountry(),
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
