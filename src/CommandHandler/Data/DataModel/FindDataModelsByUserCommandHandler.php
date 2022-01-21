<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\FindDataModelsByUserCommand;
use App\Entity\Data\DataModel\DataModel;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class FindDataModelsByUserCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @return DataModel[]
     */
    public function __invoke(FindDataModelsByUserCommand $command): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->em->getRepository(DataModel::class)->findAll();
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        $specificationPermissions = $user->getDataSpecifications()->toArray();
        $specifications = [];

        foreach ($specificationPermissions as $specificationPermission) {
            $specifications[] = $specificationPermission->getDataSpecification();
        }

        return $specifications;
    }
}
