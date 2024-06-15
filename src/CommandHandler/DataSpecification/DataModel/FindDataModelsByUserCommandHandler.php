<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\FindDataModelsByUserCommand;
use App\Entity\DataSpecification\Common\DataSpecificationPermission;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class FindDataModelsByUserCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /** @return DataModel[] */
    public function __invoke(FindDataModelsByUserCommand $command): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->em->getRepository(DataModel::class)->findAll();
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        /** @var DataSpecificationPermission[] $specificationPermissions */
        $specificationPermissions = $user->getDataSpecifications()->toArray();
        $specifications = [];

        foreach ($specificationPermissions as $specificationPermission) {
            if (! $specificationPermission->getEntity() instanceof DataModel) {
                continue;
            }

            $specifications[] = $specificationPermission->getEntity();
        }

        return $specifications;
    }
}
