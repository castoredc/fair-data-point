<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\CreateStudyCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\StudyAlreadyExists;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Security\CastorServer;
use App\Security\User;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;
use function in_array;
use function uniqid;

#[AsMessageHandler]
final class CreateStudyCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;
    private ApiClient $apiClient;

    public function __construct(EntityManagerInterface $em, Security $security, ApiClient $apiClient)
    {
        $this->em = $em;
        $this->security = $security;
        $this->apiClient = $apiClient;
    }

    /**
     * @throws NoAccessPermissionToStudy
     * @throws StudyAlreadyExists
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(CreateStudyCommand $command): Study
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $source = $command->getSource();

        $studyRepository = $this->em->getRepository(Study::class);

        if ($command->getSourceId() !== null && $studyRepository->studyExists($source, $command->getSourceId())) {
            throw new StudyAlreadyExists();
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($command->getName() . ' ' . uniqid());

        // TODO check if slug exists

        $study = null;

        if ($source->isCastor()) {
            $study = $this->createCastorStudy(
                $command->isManuallyEntered(),
                $command->getSourceId(),
                $command->getName(),
                $command->getSourceServer(),
                $slug
            );
        }

        $this->em->persist($study);
        $this->em->flush();

        return $study;
    }

    /**
     * @throws NoAccessPermissionToStudy
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     * @throws UserNotACastorUser
     */
    private function createCastorStudy(bool $manuallyEntered, ?string $sourceId, ?string $name, ?int $sourceServer, string $slug): CastorStudy
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $serverRepository = $this->em->getRepository(CastorServer::class);

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $castorUser = $user->getCastorUser();

        if ($isAdmin && $manuallyEntered) {
            $study = new CastorStudy($sourceId, $name, $slug);
            $study->setEnteredManually(true);

            $server = $serverRepository->find((int) $sourceServer);
        } else {
            if (! in_array($sourceId, $castorUser->getStudies(), true)) {
                throw new NoAccessPermissionToStudy();
            }

            $this->apiClient->setUser($castorUser);
            $study = $this->apiClient->getStudy($sourceId);

            $server = $serverRepository->findServerByUrl($castorUser->getServer());
        }

        assert($server instanceof CastorServer);

        $study->setServer($server);

        return $study;
    }
}
