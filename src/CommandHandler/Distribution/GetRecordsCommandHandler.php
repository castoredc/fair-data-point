<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\GetRecordsCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\UserNotACastorUser;
use App\Security\User;
use App\Service\CastorEntityHelper;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;
use function assert;

#[AsMessageHandler]
class GetRecordsCommandHandler
{
    private CastorEntityHelper $entityHelper;
    private Security $security;

    public function __construct(CastorEntityHelper $entityHelper, Security $security)
    {
        $this->entityHelper = $entityHelper;
        $this->security = $security;
    }

    /**
     * @return Record[]
     *
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     * @throws UserNotACastorUser
     */
    public function __invoke(GetRecordsCommand $command): array
    {
        $distribution = $command->getDistribution();

        $study = $distribution->getDataset()->getStudy();
        assert($study instanceof CastorStudy);

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $apiUser = $distribution->getApiUser();

        if ($apiUser !== null) {
            $this->entityHelper->useApiUser($apiUser);
        } else {
            if (! $user->hasCastorUser()) {
                throw new UserNotACastorUser();
            }

            $this->entityHelper->useUser($user->getCastorUser());
        }

        return $this->entityHelper->getRecords($study)->toArray();
    }
}
