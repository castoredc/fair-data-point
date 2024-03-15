<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\FindMetadataModelsByUserCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class FindMetadataModelsByUserCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return MetadataModel[] */
    public function __invoke(FindMetadataModelsByUserCommand $command): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->em->getRepository(MetadataModel::class)->findAll();
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        $specificationPermissions = $user->getDataSpecifications()->toArray();
        $specifications = [];

        foreach ($specificationPermissions as $specificationPermission) {
            $specifications[] = $specificationPermission->getEntity();
        }

        return $specifications;
    }
}
