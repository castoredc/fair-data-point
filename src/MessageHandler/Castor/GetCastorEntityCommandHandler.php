<?php
declare(strict_types=1);

namespace App\MessageHandler\Castor;

use App\Entity\Castor\CastorEntity;
use App\Message\Castor\GetCastorEntityCommand;
use App\Service\CastorEntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCastorEntityCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var CastorEntityHelper */
    private $entityHelper;

    public function __construct(EntityManagerInterface $em, CastorEntityHelper $entityHelper)
    {
        $this->em = $em;
        $this->entityHelper = $entityHelper;
    }

    public function __invoke(GetCastorEntityCommand $command): CastorEntity
    {
        $entity = $this->entityHelper->getEntityByTypeAndId($command->getStudy(), $command->getType(), $command->getId(), $command->getParentId());

        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}
