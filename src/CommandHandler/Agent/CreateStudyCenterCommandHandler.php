<?php
declare(strict_types=1);

namespace App\CommandHandler\Agent;

use App\Command\Agent\CreateStudyCenterCommand;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Exception\CountryNotFound;
use App\Exception\NoAccessPermissionToStudy;
use App\Security\Authorization\Voter\StudyVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class CreateStudyCenterCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(CreateStudyCenterCommand $command): void
    {
        if (! $this->security->isGranted(StudyVoter::EDIT, $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $country = $this->em->getRepository(Country::class)->find($command->getCountry());
        assert($country instanceof Country || $country === null);

        if ($country === null) {
            throw new CountryNotFound();
        }

        $organization = new Organization(
            null,
            $command->getName(),
            null,
            $command->getCity(),
            null,
            null,
            $country->getCode(),
        );

        $organization->setCountry($country);

        $this->em->persist($organization);

        $command->getStudy()->getLatestMetadata()->addCenter($organization);

        $this->em->persist($command->getStudy());

        $this->em->flush();
    }
}
