<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Exception\NoAccessPermission;
use App\Message\Data\DeleteDataModelPrefixCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class DeleteDataModelPrefixCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(DeleteDataModelPrefixCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $prefix = $command->getDataModelPrefix();

        $this->em->remove($prefix);

        $this->em->flush();
    }
}
