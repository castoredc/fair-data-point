<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\GetPersonByEmailCommand;
use App\Entity\FAIRData\Agent\Person;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetPersonByEmailCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetPersonByEmailCommand $command): Person
    {
        if (! $this->security->isGranted('ROLE_USER')) {
            throw new NoAccessPermission();
        }

        $repository = $this->em->getRepository(Person::class);
        $person = $repository->findOneBy(['email' => $command->getEmail()]);

        if ($person === null) {
            throw new NotFound();
        }

        return $person;
    }
}
