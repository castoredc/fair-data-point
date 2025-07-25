<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\GetStudyStructureCommand;
use App\Entity\Castor\Structure\StructureCollection\StructureCollection;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Security\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetStudyStructureCommandHandler
{
    public function __construct(private ApiClient $apiClient, private Security $security)
    {
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     * @throws UserNotACastorUser
     */
    public function __invoke(GetStudyStructureCommand $command): StructureCollection
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->apiClient->setUser($user->getCastorUser());

        return $this->apiClient->getStructure($command->getStudy());
    }
}
