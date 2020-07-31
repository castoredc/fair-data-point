<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\GetRecordsCommand;
use App\Security\CastorUser;
use App\Service\CastorEntityHelper;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class GetRecordsCommandHandler implements MessageHandlerInterface
{
    /** @var CastorEntityHelper */
    private $entityHelper;

    /** @var Security */
    private $security;

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
     */
    public function __invoke(GetRecordsCommand $command): array
    {
        $distribution = $command->getDistribution();

        $study = $distribution->getDataset()->getStudy();
        assert($study instanceof CastorStudy);

        $user = $this->security->getUser();
        assert($user instanceof CastorUser);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $apiUser = $distribution->getApiUser();

        if ($apiUser !== null) {
            $this->entityHelper->useApiUser($apiUser);
        }

        return $this->entityHelper->getRecords($study)->toArray();
    }
}
