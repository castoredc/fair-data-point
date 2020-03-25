<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\DepartmentsApiResource;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Department;
use App\Message\Api\Study\GetStudyCentersCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyCentersCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetStudyCentersCommand $message): DepartmentsApiResource
    {
        /** @var Study|null $study */
        $study = $this->em->getRepository(Study::class)->find($message->getStudyId());

        $metadata = $study->getLatestMetadata();

        // dump($metadata);

        $agents = $metadata->getCenters();

        $centers = [];

        foreach ($agents as $agent) {
            if (! ($agent instanceof Department)) {
                continue;
            }

            $centers[] = $agent;
        }

        return new DepartmentsApiResource($centers);
    }
}
