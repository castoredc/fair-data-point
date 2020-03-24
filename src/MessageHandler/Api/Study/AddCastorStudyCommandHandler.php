<?php

namespace App\MessageHandler\Api\Study;

use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\CodedText;
use App\Exception\StudyAlreadyExistsException;
use App\Message\Api\Study\AddCastorStudyCommand;
use App\Message\Api\Study\CreateStudyMetadataCommand;
use App\Model\Castor\ApiClient;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddCastorStudyCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(EntityManagerInterface $em, ApiClient $apiClient)
    {
        $this->em = $em;
        $this->apiClient = $apiClient;
    }

    public function __invoke(AddCastorStudyCommand $message)
    {
        $this->apiClient->setToken($message->getUser()->getToken());

        $study = $this->apiClient->getStudy($message->getStudyId());

        try {
            $this->em->persist($study);
            $this->em->flush();
        } catch(UniqueConstraintViolationException $e)
        {
            throw new StudyAlreadyExistsException();
        }
    }
}