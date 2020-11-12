<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\GetDataModelsCommand;
use App\Entity\Data\DataModel\DataModel;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetDataModelsCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /** @return DataModel[] */
    public function __invoke(GetDataModelsCommand $command): array
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        return $this->em->getRepository(DataModel::class)->findAll();
    }
}
