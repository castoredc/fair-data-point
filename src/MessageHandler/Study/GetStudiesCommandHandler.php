<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Study;
use App\Exception\UserNotACastorUser;
use App\Message\Study\GetStudiesCommand;
use App\Security\User;
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
     *
     * @throws UserNotACastorUser
     */
    public function __invoke(GetStudiesCommand $message): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $dbStudies = $this->em->getRepository(Study::class)->findAll();
        } else {
            /** @var User $user */
            $user = $this->security->getUser();

            if (! $user->hasCastorUser()) {
                throw new UserNotACastorUser();
            }

            $userStudies = $user->getCastorUser()->getStudies();
            $dbStudies = $this->em->getRepository(Study::class)->findBy(['id' => $userStudies]);
        }

        return $dbStudies;
    }
}
