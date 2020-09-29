<?php
declare(strict_types=1);

namespace App\MessageHandler\Security;

use App\Entity\Enum\NameOrigin;
use App\Entity\FAIRData\Person;
use App\Entity\Iri;
use App\Message\Security\UpdateUserCommand;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class UpdateUserCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        // We currently only allow people with an ORCID to change their details
        if (! $user->hasOrcid()) {
            return;
        }

        if ($user->getPerson() !== null && ! $user->getPerson()->getNameOrigin()->isOrcid()) {
            return;
        }

        $dbUser = $this->em->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        assert($dbUser instanceof User || $dbUser === null);
        $dbPerson = $dbUser->getPerson();

        if ($dbPerson !== null) {
            $dbPerson->setFirstName($command->getFirstName());
            $dbPerson->setMiddleName($command->getMiddleName());
            $dbPerson->setLastName($command->getLastName());
            $dbPerson->setEmail($command->getEmail());
            $dbPerson->setNameOrigin(NameOrigin::user());

            $person = $dbPerson;
        } else {
            $orcid = $user->hasOrcid() ? new Iri($user->getOrcid()->getOrcid()) : null;
            $person = new Person($command->getFirstName(), $command->getMiddleName(), $command->getLastName(), $command->getEmail(), null, $orcid, NameOrigin::user());
        }

        $dbUser->setPerson($person);
        $person->setUser($dbUser);

        $this->em->persist($person);
        $this->em->persist($dbUser);
        $this->em->flush();
    }
}
