<?php
declare(strict_types=1);

namespace App\MessageHandler\Security;

use App\Entity\Enum\NameOrigin;
use App\Message\Security\UpdateUserCommand;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class UpdateUserCommandHandler implements MessageHandlerInterface
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

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->getNameOrigin()->isOrcid()) {
            return;
        }

        /** @var User|null $dbUser */
        $dbUser = $this->em->getRepository(User::class)->findOneBy(['id' => $user->getId()]);

        $dbUser->setNameFirst($command->getFirstName());
        $dbUser->setNameMiddle($command->getMiddleName());
        $dbUser->setNameLast($command->getLastName());
        $dbUser->setEmailAddress($command->getEmail());
        $dbUser->setNameOrigin(NameOrigin::user());

        $this->em->persist($dbUser);
        $this->em->flush();
    }
}
