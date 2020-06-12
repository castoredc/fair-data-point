<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Study;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyNotFound;
use App\Message\Study\GetStudiesCommand;
use App\Message\Study\GetStudyCommand;
use App\Security\CastorUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetStudyCommandHandler implements MessageHandlerInterface
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

    public function __invoke(GetStudyCommand $command): Study
    {
        $study = $this->em->getRepository(Study::class)->find($command->getId());

        if($study === null) {
            throw new StudyNotFound();
        }

        if(! $this->security->isGranted('view', $study)) {
            throw new NoAccessPermissionToStudy();
        }

        return $study;
    }
}
