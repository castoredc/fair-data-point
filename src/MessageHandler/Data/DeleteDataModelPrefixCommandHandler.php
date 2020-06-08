<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Message\Data\DeleteDataModelPrefixCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteDataModelPrefixCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteDataModelPrefixCommand $command): void
    {
        $prefix = $command->getDataModelPrefix();

        $this->em->remove($prefix);

        $this->em->flush();
    }
}
