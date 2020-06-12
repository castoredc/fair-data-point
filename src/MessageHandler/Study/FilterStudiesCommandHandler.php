<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Study;
use App\Entity\PaginatedResultCollection;
use App\Message\Study\FilterStudiesCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Repository\StudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class FilterStudiesCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return Study[] */
    public function __invoke(FilterStudiesCommand $message): array
    {
        /** @var StudyRepository $datasetRepository */
        $datasetRepository = $this->em->getRepository(Study::class);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $studies = $datasetRepository->findStudies(
            $message->getCatalog(),
            $message->getSearch(),
            $message->getStudyType(),
            $message->getMethodType(),
            $message->getCountry(),
            null,
            null,
            $isAdmin
        );

        return $studies;
    }
}
