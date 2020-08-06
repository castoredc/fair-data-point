<?php
declare(strict_types=1);

namespace App\MessageHandler\Study;

use App\Entity\Study;
use App\Exception\UserNotACastorUser;
use App\Message\Study\FindStudiesByUserCommand;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FindStudiesByUserCommandHandler implements MessageHandlerInterface
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
     * @return array<Study>
     *
     * @throws UserNotACastorUser
     */
    public function __invoke(FindStudiesByUserCommand $message): array
    {
        $user = $message->getUser();

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->apiClient->setUser($user->getCastorUser());

        $castorStudies = $this->apiClient->getStudies();
        $castorStudyIds = $this->apiClient->getStudyIds($castorStudies);

        $dbStudies = $this->em->getRepository(Study::class)->findBy(['id' => $castorStudyIds]);

        if ($message->getLoadFromCastor() && $message->getHideExistingStudies()) {
            $studies = [];

            foreach ($castorStudies as $castorStudy) {
                $include = true;
                foreach ($dbStudies as $study) {
                    if ($castorStudy->getId() !== $study->getId()) {
                        continue;
                    }

                    $include = false;
                }

                if (! $include) {
                    continue;
                }

                $studies[] = $castorStudy;
            }

            return $studies;
        }
        if ($message->getLoadFromCastor() && ! $message->getHideExistingStudies()) {
            return $castorStudies;
        }

        return $dbStudies;
    }
}
