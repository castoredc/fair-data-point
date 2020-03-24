<?php

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

    public function __invoke(FindStudiesByUserCommand $message)
    {
        $this->apiClient->setToken($message->getUser()->getToken());

        $castorStudies = $this->apiClient->getStudies();
        $castorStudyIds = $this->getStudyIds($castorStudies);

        $dbStudies = $this->em->getRepository(Study::class)->findBy(['id' => $castorStudyIds]);

        if($message->getLoadFromCastor() && $message->getHideExistingStudies()) {
            $studies = [];

            foreach ($castorStudies as $castorStudy) {
                $include = true;
                foreach ($dbStudies as $study) {
                    if ($castorStudy->getId() === $study->getId()) {
                        $include = false;
                    }
                }

                if ($include) {
                    $studies[] = $castorStudy;
                }
            }

            return $studies;
        }
        else if($message->getLoadFromCastor() && !$message->getHideExistingStudies()) {
            return $castorStudies;
        }
        else {
            return $dbStudies;
        }
    }

    /**
     * @param array<Study> $castorStudies
     *
     * @return array<string>
     */
    private function getStudyIds(array $castorStudies)
    {
        $castorStudyIds = [];

        foreach($castorStudies as $castorStudy) {
            $castorStudyIds[] = $castorStudy->getId();
        }
        return $castorStudyIds;
    }
}