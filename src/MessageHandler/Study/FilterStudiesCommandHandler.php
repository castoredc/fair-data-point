<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Study;
use App\Message\Study\FilterStudiesCommand;
use App\Repository\StudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class FilterStudiesCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return Study[] */
    public function __invoke(FilterStudiesCommand $message): array
    {
        $datasetRepository = $this->em->getRepository(Study::class);
        assert($datasetRepository instanceof StudyRepository);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        return $datasetRepository->findStudies(
            $message->getCatalog(),
            null,
            $message->getSearch(),
            $message->getStudyType(),
            $message->getMethodType(),
            $message->getCountry(),
            null,
            null,
            $isAdmin
        );
    }
}
