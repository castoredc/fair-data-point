<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Exception\StudyAlreadyExists;
use App\Message\Api\Study\AddManualCastorStudyCommand;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddManualCastorStudyCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(AddManualCastorStudyCommand $message): void
    {
        $study = new Study($message->getStudyId(), $message->getStudyName(), null, $message->getStudySlug(), null);
        $study->setEnteredManually(true);

        try {
            $this->em->persist($study);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new StudyAlreadyExists();
        }
    }
}
