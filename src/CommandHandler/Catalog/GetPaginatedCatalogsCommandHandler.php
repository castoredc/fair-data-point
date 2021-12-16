<?php
declare(strict_types=1);

namespace App\CommandHandler\Catalog;

use App\Command\Catalog\GetPaginatedCatalogsCommand;
use App\Entity\FAIRData\Catalog;
use App\Entity\PaginatedResultCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetPaginatedCatalogsCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetPaginatedCatalogsCommand $command): PaginatedResultCollection
    {
        $catalogRepository = $this->em->getRepository(Catalog::class);

        return new PaginatedResultCollection(
            $catalogRepository->findCatalogs($command->getAgent(), $command->getAcceptSubmissions(), $command->getPerPage(), $command->getPage()),
            $command->getPage(),
            $command->getPerPage(),
            $catalogRepository->countCatalogs($command->getAgent(), $command->getAcceptSubmissions())
        );
    }
}
