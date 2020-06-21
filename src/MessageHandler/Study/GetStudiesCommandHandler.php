<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Study;
use App\Message\Study\GetStudiesCommand;
use App\Security\CastorUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetStudiesCommandHandler implements MessageHandlerInterface
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

    /**
     * @return Study[]
     */
    public function __invoke(GetStudiesCommand $message): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $dbStudies = $this->em->getRepository(Study::class)->findAll();
        } else {
            /** @var CastorUser $user */
            $user = $this->security->getUser();
            $userStudies = $user->getStudies();
            $dbStudies = $this->em->getRepository(Study::class)->findBy(['id' => $userStudies]);
        }

        return $dbStudies;
    }
}
