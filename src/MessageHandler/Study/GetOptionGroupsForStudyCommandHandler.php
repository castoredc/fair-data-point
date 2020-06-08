<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Enum\CastorEntityType;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Study\GetOptionGroupsForStudyCommand;
use App\Model\Castor\CastorEntityCollection;
use App\Service\CastorEntityHelper;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetOptionGroupsForStudyCommandHandler implements MessageHandlerInterface
{
    /** @var CastorEntityHelper */
    private $entityHelper;

    public function __construct(CastorEntityHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(GetOptionGroupsForStudyCommand $command): CastorEntityCollection
    {
        return $this->entityHelper->getEntitiesByType($command->getStudy(), CastorEntityType::fieldOptionGroup());
    }
}
