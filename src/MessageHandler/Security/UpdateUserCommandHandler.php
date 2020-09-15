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

        if (! $user->hasOrcid() || ! $user->getPerson()->getNameOrigin()->isOrcid()) {
            // We currently only allow people with an ORCID to change their details
            return;
        }

        /** @var User|null $dbUser */
        $dbUser = $this->em->getRepository(User::class)->findOneBy(['id' => $user->getId()]);

        $dbUser->getPerson()->setFirstName($command->getFirstName());
        $dbUser->getPerson()->setMiddleName($command->getMiddleName());
        $dbUser->getPerson()->setLastName($command->getLastName());
        $dbUser->getPerson()->setEmail($command->getEmail());
        $dbUser->getPerson()->setNameOrigin(NameOrigin::user());

        $this->em->persist($dbUser);
        $this->em->flush();
    }
}
