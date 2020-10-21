<?php
declare(strict_types=1);

namespace App\MessageHandler\Agent;

use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Country;
use App\Exception\CountryNotFound;
use App\Exception\NoAccessPermission;
use App\Message\Agent\FindOrganizationsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class FindOrganizationsCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return Organization[] */
    public function __invoke(FindOrganizationsCommand $command): array
    {
        if (! $this->security->isGranted('ROLE_USER')) {
            throw new NoAccessPermission();
        }

        $country = $this->em->getRepository(Country::class)->find($command->getCountry());
        assert($country instanceof Country || $country === null);

        if ($country === null) {
            throw new CountryNotFound();
        }

        $repository = $this->em->getRepository(Organization::class);

        return $repository->findOrganizations($country, $command->getSearch());
    }
}
