<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\FindDataModelsByUserCommand;
use App\Entity\Data\DataModel\DataModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

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
        return $this->em->getRepository(DataModel::class)->findAll();
    }
}
