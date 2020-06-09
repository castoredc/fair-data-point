<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\Study;
use App\Entity\PaginatedResultCollection;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Repository\StudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetPaginatedStudiesCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetPaginatedStudiesCommand $message): PaginatedResultCollection
    {
        /** @var StudyRepository $datasetRepository */
        $datasetRepository = $this->em->getRepository(Study::class);

        $count = $datasetRepository->countStudies($message->getCatalog(), $message->getSearch(), $message->getStudyType(), $message->getMethodType(), $message->getCountry());
        $studies = $datasetRepository->findStudies($message->getCatalog(), $message->getSearch(), $message->getStudyType(), $message->getMethodType(), $message->getCountry(), $message->getPerPage(), $message->getPage(), false);

        return new PaginatedResultCollection($studies, $message->getPage(), $message->getPerPage(), $count);
    }
}
