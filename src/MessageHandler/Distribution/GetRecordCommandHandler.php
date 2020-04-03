<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\Record;
use App\Message\Distribution\GetRecordCommand;
use App\Model\Castor\ApiClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetRecordCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return Record[]
     */
    public function __invoke(GetRecordCommand $message): array
    {
        $this->apiClient->setUser($message->getUser());

        return [$this->apiClient->getRecord($message->getStudy(), $message->getRecordId())];
    }
}
