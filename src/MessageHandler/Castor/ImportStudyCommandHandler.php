<?php
declare(strict_types=1);

namespace App\MessageHandler\Castor;

use App\Entity\Enum\StudySource;
use App\Entity\Study;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\StudyAlreadyExists;
use App\Message\Castor\ImportStudyCommand;
use App\Model\Castor\ApiClient;
use App\Repository\CastorServerRepository;
use App\Repository\StudyRepository;
use App\Security\CastorServer;
use App\Security\CastorUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class ImportStudyCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(EntityManagerInterface $em, Security $security, ApiClient $apiClient)
    {
        $this->em = $em;
        $this->security = $security;
        $this->apiClient = $apiClient;
    }

    /**
     * @throws StudyAlreadyExists
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(ImportStudyCommand $command): Study
    {
        $user = $this->security->getUser();
        assert($user instanceof CastorUser);

        $this->apiClient->setUser($user);

        /** @var StudyRepository $studyRepository */
        $studyRepository = $this->em->getRepository(Study::class);

        if ($studyRepository->studyExists(StudySource::castor(), $command->getId())) {
            $study = $studyRepository->findStudyBySourceAndId(StudySource::castor(), $command->getId());

            if (! $this->security->isGranted('edit', $study)) {
                throw new NoAccessPermissionToStudy();
            }

            return $study;
        }

        /** @var CastorServerRepository $serverRepository */
        $serverRepository = $this->em->getRepository(CastorServer::class);
        $server = $serverRepository->findBy(['url' => $user->getServer()]);
        assert($server instanceof CastorServer);

        $study = $this->apiClient->getStudy($command->getId());
        $study->setServer($server);

        $this->em->persist($study);
        $this->em->flush();

        return $study;
    }
}
