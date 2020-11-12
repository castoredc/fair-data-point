<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Entity\Study;
use App\Command\Study\FilterStudiesCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

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
    public function __invoke(FilterStudiesCommand $command): array
    {
        $datasetRepository = $this->em->getRepository(Study::class);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        return $datasetRepository->findStudies(
            $command->getCatalog(),
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
