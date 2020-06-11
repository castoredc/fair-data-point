<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\StudySource;
use App\Entity\Study;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\StudyAlreadyExists;
use App\Message\Study\AddStudyCommand;
use App\Model\Castor\ApiClient;
use App\Repository\CastorServerRepository;
use App\Repository\StudyRepository;
use App\Security\CastorServer;
use App\Security\CastorUser;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class AddStudyCommandHandler implements MessageHandlerInterface
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
     * @param AddStudyCommand $command
     *
     * @return Study
     * @throws NoAccessPermissionToStudy
     * @throws StudyAlreadyExists
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(AddStudyCommand $command): Study
    {
        $source = $command->getSource();

        /** @var StudyRepository $repository */
        $repository = $this->em->getRepository(Study::class);

        if($command->getSourceId() !== null && $repository->studyExists($source, $command->getSourceId()))
        {
            throw new StudyAlreadyExists();
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($command->getName());

        // TODO check if slug exists

        $study = null;

        if($source->isCastor()) {
            $study = $this->createCastorStudy($command->isManuallyEntered(), $command->getSourceId(), $command->getName(), $command->getSourceServer(), $slug);
        }

        $this->em->persist($study);
        $this->em->flush();

        return $study;
    }

    /**
     * @param bool        $manuallyEntered
     * @param string|null $sourceId
     * @param string|null $name
     * @param string|null $sourceServer
     * @param string      $slug
     *
     * @return CastorStudy
     * @throws NoAccessPermissionToStudy
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function createCastorStudy(bool $manuallyEntered, ?string $sourceId, ?string $name, ?string $sourceServer, string $slug): CastorStudy
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        /** @var CastorServerRepository $serverRepository */
        $serverRepository = $this->em->getRepository(CastorServer::class);

        /** @var CastorUser $user */
        $user = $this->security->getUser();

        if($isAdmin && $manuallyEntered) {
            $study = new CastorStudy($sourceId, $name, $slug);
            $study->setEnteredManually(true);

            $server = $serverRepository->find($sourceServer);
        } else {
            if (! in_array($sourceId, $user->getStudies(), true)) {
                throw new NoAccessPermissionToStudy();
            }

            $this->apiClient->setUser($user);
            $study = $this->apiClient->getStudy($sourceId);

            $server = $serverRepository->findServerByUrl($user->getServer());
        }

        $study->setServer($server);

        return $study;
    }
}
