<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\CastorServer;
use App\Entity\Castor\Structure\Phase;
use App\Entity\Castor\Structure\StructureCollection\PhaseCollection;
use App\Entity\Castor\Structure\StructureCollection\StructureCollection;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Language;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\StudyAlreadyExists;
use App\Message\Study\AddCastorStudyCommand;
use App\Message\Study\GetStudyStructureCommand;
use App\Model\Castor\ApiClient;
use App\Repository\CastorServerRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function in_array;
use function uniqid;

class GetStudyStructureCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(EntityManagerInterface $em, ApiClient $apiClient)
    {
        $this->em = $em;
        $this->apiClient = $apiClient;
    }

    /**
     * @param GetStudyStructureCommand $message
     *
     * @return StructureCollection
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
