<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\FindStudiesByUserCommand;
use App\Entity\Study;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FindStudiesByUserCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private ApiClient $apiClient;

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
    public function __invoke(FindStudiesByUserCommand $command): array
    {
        $user = $command->getUser();

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->apiClient->setUser($user->getCastorUser());

        $castorStudies = $this->apiClient->getStudies();
        $castorStudyIds = $this->apiClient->getStudyIds($castorStudies);

        $dbStudies = $this->em->getRepository(Study::class)->findBy(['id' => $castorStudyIds]);

        if ($command->getLoadFromCastor() && $command->getHideExistingStudies()) {
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

        if ($command->getLoadFromCastor() && ! $command->getHideExistingStudies()) {
            return $castorStudies;
        }

        return $dbStudies;
    }
}
