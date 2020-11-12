<?php
declare(strict_types=1);

namespace App\CommandHandler\Study\Provenance;

use App\Api\Resource\Agent\Person\PersonsApiResource;
use App\Command\Study\Provenance\GetStudyContactsCommand;
use App\Entity\FAIRData\Agent\Person;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyContactsCommandHandler implements MessageHandlerInterface
{
    public function __invoke(GetStudyContactsCommand $command): PersonsApiResource
    {
        $metadata = $command->getStudy()->getLatestMetadata();
        $agents = $metadata->getContacts();

        $persons = [];

        foreach ($agents as $agent) {
            if (! ($agent instanceof Person)) {
                continue;
            }

            $persons[] = $agent;
        }

        return new PersonsApiResource($persons);
    }
}
