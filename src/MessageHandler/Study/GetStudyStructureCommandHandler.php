<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\Structure\StructureCollection\StructureCollection;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Study\GetStudyStructureCommand;
use App\Model\Castor\ApiClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyStructureCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(GetStudyStructureCommand $message): StructureCollection
    {
        $this->apiClient->setUser($message->getUser());

        return $this->apiClient->getStructure($message->getStudy());
    }
}
