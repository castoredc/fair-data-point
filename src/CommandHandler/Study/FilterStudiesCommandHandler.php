<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\FilterStudiesCommand;
use App\Entity\Study;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FilterStudiesCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /** @return Study[] */
    public function __invoke(FilterStudiesCommand $command): array
    {
        $studyRepository = $this->em->getRepository(Study::class);

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
