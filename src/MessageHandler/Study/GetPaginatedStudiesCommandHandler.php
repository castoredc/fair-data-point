<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\PaginatedResultCollection;
use App\Entity\Study;
use App\Message\Study\GetPaginatedStudiesCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetPaginatedStudiesCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetPaginatedStudiesCommand $message): PaginatedResultCollection
    {
        $datasetRepository = $this->em->getRepository(Study::class);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $count = $datasetRepository->countStudies(
            $message->getCatalog(),
            $message->getHideCatalogs(),
            $message->getSearch(),
            $message->getStudyType(),
            $message->getMethodType(),
            $message->getCountry(),
            $isAdmin
        );

        $studies = $datasetRepository->findStudies(
            $message->getCatalog(),
            $message->getHideCatalogs(),
            $message->getSearch(),
            $message->getStudyType(),
            $message->getMethodType(),
            $message->getCountry(),
            $message->getPerPage(),
            $message->getPage(),
            $isAdmin
        );

        return new PaginatedResultCollection($studies, $message->getPage(), $message->getPerPage(), $count);
    }
}
