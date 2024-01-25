<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\GetStudyCommand;
use App\Entity\Study;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetStudyCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetStudyCommand $command): Study
    {
        $study = $this->em->getRepository(Study::class)->find($command->getId());

        if ($study === null) {
            throw new StudyNotFound();
        }

        if (! $this->security->isGranted('view', $study)) {
            throw new NoAccessPermissionToStudy();
        }

        return $study;
    }
}
