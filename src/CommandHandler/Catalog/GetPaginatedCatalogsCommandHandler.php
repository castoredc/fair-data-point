<?php
declare(strict_types=1);

namespace App\CommandHandler\Catalog;

use App\Command\Catalog\GetPaginatedCatalogsCommand;
use App\Entity\FAIRData\Catalog;
use App\Entity\PaginatedResultCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetPaginatedCatalogsCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(GetPaginatedCatalogsCommand $command): PaginatedResultCollection
    {
        $catalogRepository = $this->em->getRepository(Catalog::class);

        return new PaginatedResultCollection(
            $catalogRepository->findCatalogs(
                $command->getAgent(),
                $command->getAcceptSubmissions(),
                $command->getPerPage(),
                $command->getPage(),
                $command->getSearch(),
                $command->getUser()
            ),
            $command->getPage(),
            $command->getPerPage(),
            $catalogRepository->countCatalogs(
                $command->getAgent(),
                $command->getAcceptSubmissions(),
                $command->getSearch(),
                $command->getUser()
            )
        );
    }
}
