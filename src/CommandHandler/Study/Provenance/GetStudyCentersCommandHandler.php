<?php
declare(strict_types=1);

namespace App\CommandHandler\Study\Provenance;

use App\Api\Resource\Agent\Department\DepartmentsApiResource;
use App\Command\Study\Provenance\GetStudyCentersCommand;
use App\Entity\FAIRData\Agent\Department;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyCentersCommandHandler implements MessageHandlerInterface
{
    public function __invoke(GetStudyCentersCommand $command): DepartmentsApiResource
    {
        $metadata = $command->getStudy()->getLatestMetadata();
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

        return new DepartmentsApiResource($centers, true);
    }
}
