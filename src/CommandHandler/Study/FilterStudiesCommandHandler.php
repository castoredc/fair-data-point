<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\FilterStudiesCommand;
use App\Entity\Study;
use App\Repository\StudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class FilterStudiesCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return Study[] */
    public function __invoke(FilterStudiesCommand $command): array
    {
        $studyRepository = $this->em->getRepository(Study::class);
        assert($studyRepository instanceof StudyRepository);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        return $studyRepository->findStudies(
            $command->getCatalog(),
            $command->getAgent(),
            null,
            $command->getSearch(),
            $command->getStudyType(),
            $command->getMethodType(),
            $command->getCountry(),
            null,
            null,
            $isAdmin
        );
    }
}
