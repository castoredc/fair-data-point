<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\Record;
use App\Message\Distribution\GetRecordsCommand;
use App\Model\Castor\ApiClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetRecordsCommandHandler implements MessageHandlerInterface
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
    public function __invoke(GetRecordsCommand $message): array
    {
        $this->apiClient->setToken($message->getUser()->getToken());

        return $this->apiClient->getRecords($message->getStudy())->toArray();
    }
}
