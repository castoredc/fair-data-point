<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\Form\FieldOption;
use App\Entity\Castor\Form\FieldOptionGroup;
use App\Entity\Enum\CastorEntityType;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Study\GetOptionGroupsForStudyCommand;
use App\Model\Castor\ApiClient;
use App\Model\Castor\CastorEntityCollection;
use App\Repository\CastorEntityRepository;
use App\Service\CastorEntityHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetOptionGroupsForStudyCommandHandler implements MessageHandlerInterface
{
    /** @var CastorEntityHelper */
    private $entityHelper;

    public function __construct(EntityManagerInterface $em, CastorEntityHelper $entityHelper)
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
