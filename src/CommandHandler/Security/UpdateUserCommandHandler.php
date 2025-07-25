<?php
declare(strict_types=1);

namespace App\CommandHandler\Security;

use App\Command\Security\UpdateUserCommand;
use App\Entity\Enum\NameOrigin;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\Iri;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class UpdateUserCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
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
        $dbPerson = $dbUser->getPerson();

        if ($dbPerson !== null) {
            $dbPerson->setFirstName($command->getFirstName());
            $dbPerson->setMiddleName($command->getMiddleName());
            $dbPerson->setLastName($command->getLastName());
            $dbPerson->setEmail($command->getEmail());
            $dbPerson->setNameOrigin(NameOrigin::user());

            $person = $dbPerson;
        } else {
            $orcid = new Iri($user->getOrcid()->getOrcid());
            $person = new Person($command->getFirstName(), $command->getMiddleName(), $command->getLastName(), $command->getEmail(), null, $orcid, NameOrigin::user());
        }

        $dbUser->setPerson($person);
        $person->setUser($dbUser);

        $this->em->persist($person);
        $this->em->persist($dbUser);
        $this->em->flush();
    }
}
