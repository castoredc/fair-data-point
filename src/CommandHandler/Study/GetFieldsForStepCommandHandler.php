<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Entity\Castor\Form\Field;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\UserNotACastorUser;
use App\Command\Study\GetFieldsForStepCommand;
use App\Model\Castor\ApiClient;
use App\Security\User;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class GetFieldsForStepCommandHandler implements MessageHandlerInterface
{
    private ApiClient $apiClient;

    private Security $security;

    public function __construct(ApiClient $apiClient, Security $security)
    {
        $this->apiClient = $apiClient;
        $this->security = $security;
    }

    /**
     * @return Field[]
     *
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     * @throws UserNotACastorUser
     */
    public function __invoke(GetFieldsForStepCommand $command): array
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->apiClient->setUser($user->getCastorUser());

        return $this->apiClient->getFieldByParent($command->getStudy(), $command->getStepId())->toArray();
    }
}
