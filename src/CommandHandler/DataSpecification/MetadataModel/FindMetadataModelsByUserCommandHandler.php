<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\FindMetadataModelsByUserCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function array_merge;
use function array_unique;
use function assert;
use const SORT_REGULAR;

#[AsMessageHandler]
class FindMetadataModelsByUserCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /** @return MetadataModel[] */
    public function __invoke(FindMetadataModelsByUserCommand $command): array
    {
        $metadataRepository = $this->em->getRepository(MetadataModel::class);

        $user = $this->security->getUser();
        assert($user instanceof User);

        return array_unique(array_merge(
            $metadataRepository->findByUser($user),
            $metadataRepository->findInUseByEntitiesUserHasPermissionsTo($user)
        ), SORT_REGULAR);
    }
}
