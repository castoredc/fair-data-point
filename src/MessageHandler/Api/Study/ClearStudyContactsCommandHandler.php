<?php

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Entity\Metadata\StudyMetadata;
use App\Exception\StudyAlreadyExistsException;
use App\Exception\StudyNotFoundException;
use App\Message\Api\Study\AddCastorStudyCommand;
use App\Message\Api\Study\ClearStudyCentersCommand;
use App\Message\Api\Study\ClearStudyContactsCommand;
use App\Model\Castor\ApiClient;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ClearStudyContactsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(ClearStudyContactsCommand $message)
    {
        /** @var Study|null $study */
        $study = $this->em->getRepository(Study::class)->find($message->getStudyId());

        if(!$study)
        {
            throw new StudyNotFoundException();
        }

        $metadata = $study->getLatestMetadata();
        $metadata->setContacts([]);

        $this->em->persist($metadata);
        $this->em->flush();
    }
}