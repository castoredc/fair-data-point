<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\StudyAlreadyExists;
use App\Message\Study\CreateStudyCommand;
use App\Message\Study\UpdateStudyCommand;
use App\Model\Castor\ApiClient;
use App\Repository\CastorServerRepository;
use App\Repository\StudyRepository;
use App\Security\CastorServer;
use App\Security\CastorUser;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function in_array;

class UpdateStudyCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security, ApiClient $apiClient)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(UpdateStudyCommand $command): Study
    {
        $study = $command->getStudy();

        if(! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermissionToStudy();
        }

        $study->setSlug($command->getSlug());
        $study->setName($command->getName());
        $study->setSourceId($command->getSourceId());
        $study->setIsPublished($command->isPublished());

        if ($study instanceof CastorStudy) {
            $server = $this->em->getRepository(CastorServer::class)->find($command->getSourceServer());
            assert($server instanceof CastorServer);

            $study->setServer($server);
        }

        $this->em->persist($study);
        $this->em->flush();

        return $study;
    }
}
