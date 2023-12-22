<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\GetOptionGroupsForStudyCommand;
use App\Entity\Enum\CastorEntityType;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\CastorEntityCollection;
use App\Security\User;
use App\Service\CastorEntityHelper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetOptionGroupsForStudyCommandHandler
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
