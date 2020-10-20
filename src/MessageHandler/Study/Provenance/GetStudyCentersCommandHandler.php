<?php
declare(strict_types=1);

namespace App\MessageHandler\Study\Provenance;

use App\Api\Resource\Agent\Department\DepartmentsApiResource;
use App\Entity\FAIRData\Agent\Department;
use App\Message\Study\Provenance\GetStudyCentersCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyCentersCommandHandler implements MessageHandlerInterface
{
    public function __invoke(GetStudyCentersCommand $message): DepartmentsApiResource
    {
        $metadata = $message->getStudy()->getLatestMetadata();
        $centers = [];

        if ($metadata !== null) {
            $agents = $metadata->getCenters();

            $centers = [];

            foreach ($agents as $agent) {
                if (! ($agent instanceof Department)) {
                    continue;
                }

                $centers[] = $agent;
            }
        }

        return new DepartmentsApiResource($centers);
    }
}
