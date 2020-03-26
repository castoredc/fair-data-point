<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Exception\StudyNotFoundException;
use App\Message\Api\Study\ClearStudyCentersCommand;
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
        $metadata->setCenters([]);

        $this->em->persist($metadata);
        $this->em->flush();
    }
}
