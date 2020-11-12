<?php
declare(strict_types=1);

namespace App\CommandHandler\Study\Provenance;

use App\Command\Study\Provenance\ClearStudyCentersCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClearStudyCentersCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(ClearStudyCentersCommand $command): void
    {
        $metadata = $command->getStudy()->getLatestMetadata();
        if ($metadata === null) {
            return;
        }

        $metadata->setCenters(new ArrayCollection());

        $this->em->persist($metadata);
        $this->em->flush();
    }
}
