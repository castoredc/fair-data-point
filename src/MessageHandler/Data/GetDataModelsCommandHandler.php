<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\DataModel;
use App\Message\Data\GetDataModelsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetDataModelsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @return DataModel[] */
    public function __invoke(GetDataModelsCommand $command): array
    {
        return $this->em->getRepository(DataModel::class)->findAll();
    }
}
