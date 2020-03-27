<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\DepartmentsApiResource;
use App\Entity\FAIRData\Department;
use App\Message\Api\Study\GetStudyCentersCommand;
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
