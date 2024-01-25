<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\GetStudiesForAgentCommand;
use App\Entity\Study;
use App\Repository\StudyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetStudiesForAgentCommandHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @return Study[] */
    public function __invoke(GetStudiesForAgentCommand $command): array
    {
        $studyRepository = $this->em->getRepository(Study::class);
        assert($studyRepository instanceof StudyRepository);

        return $studyRepository->getByAgent($command->getAgent());
    }
}
