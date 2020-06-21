<?php
declare(strict_types=1);

namespace App\MessageHandler\Castor;

use App\Entity\Castor\CastorEntity;
use App\Exception\NoAccessPermissionToStudy;
use App\Message\Castor\GetCastorEntityCommand;
use App\Service\CastorEntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetCastorEntityCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var CastorEntityHelper */
    private $entityHelper;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, CastorEntityHelper $entityHelper, Security $security)
    {
        $this->em = $em;
        $this->entityHelper = $entityHelper;
        $this->security = $security;
    }

    public function __invoke(GetCastorEntityCommand $command): CastorEntity
    {
        if (! $this->security->isGranted('edit', $command->getStudy())) {
            throw new NoAccessPermissionToStudy();
        }

        $entity = $this->entityHelper->getEntityByTypeAndId($command->getStudy(), $command->getType(), $command->getId(), $command->getParentId());

        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}
