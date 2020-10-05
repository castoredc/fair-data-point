<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Enum\CastorEntityType;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\UserNotACastorUser;
use App\Message\Study\GetOptionGroupsForStudyCommand;
use App\Model\Castor\CastorEntityCollection;
use App\Security\User;
use App\Service\CastorEntityHelper;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class GetOptionGroupsForStudyCommandHandler implements MessageHandlerInterface
{
    private CastorEntityHelper $entityHelper;

    private Security $security;

    public function __construct(CastorEntityHelper $entityHelper, Security $security)
    {
        $this->entityHelper = $entityHelper;
        $this->security = $security;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     * @throws UserNotACastorUser
     */
    public function __invoke(GetOptionGroupsForStudyCommand $command): CastorEntityCollection
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->entityHelper->useUser($user->getCastorUser());

        $optionGroups = $this->entityHelper->getEntitiesByType($command->getStudy(), CastorEntityType::fieldOptionGroup());
        $optionGroups->orderByLabel();

        return $optionGroups;
    }
}
