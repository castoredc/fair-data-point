<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Message\Api\Study\FindStudiesByUserCommand;
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
     */
    public function __invoke(FindStudiesByUserCommand $message): array
    {
        $this->apiClient->setToken($message->getUser()->getToken());

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
