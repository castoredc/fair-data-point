<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Exception\StudyAlreadyExists;
use App\Message\Api\Study\AddCastorStudyCommand;
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

    public function __invoke(AddCastorStudyCommand $message): void
    {
        $this->apiClient->setToken($message->getUser()->getToken());

        $study = $this->apiClient->getStudy($message->getStudyId());

        try {
            $this->em->persist($study);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new StudyAlreadyExists();
        }
    }
}
