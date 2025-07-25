<?php
declare(strict_types=1);

namespace App\CommandHandler\Catalog;

use App\Command\Catalog\FindCatalogsByUserCommand;
use App\Entity\FAIRData\Catalog;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class FindCatalogsByUserCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /** @return Catalog[] */
    public function __invoke(FindCatalogsByUserCommand $command): array
    {
        $catalogRepository = $this->em->getRepository(Catalog::class);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            /** @var Catalog[] $catalogs */
            $catalogs = $catalogRepository->findAll();

            return $catalogs;
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        $permissions = $user->getCatalogs()->toArray();
        $catalogs = [];

        foreach ($permissions as $permission) {
            $catalogs[] = $permission->getEntity();
        }

        return $catalogs;
    }
}
