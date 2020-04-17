<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\Form\Field;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Study\GetFieldsForStepCommand;
use App\Model\Castor\ApiClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetFieldsForStepCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return Field[]
     *
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(GetFieldsForStepCommand $message): array
    {
        $this->apiClient->setUser($message->getUser());

        return $this->apiClient->getFieldByParent($message->getStudy(), $message->getStepId())->toArray();
    }
}
