<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Message\Api\Study\ClearStudyCentersCommand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClearStudyCentersCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(ClearStudyCentersCommand $message): void
    {
        $metadata = $message->getStudy()->getLatestMetadata();
        if ($metadata === null) {
            return;
        }

        $metadata->setCenters(new ArrayCollection());

        $this->em->persist($metadata);
        $this->em->flush();
    }
}
