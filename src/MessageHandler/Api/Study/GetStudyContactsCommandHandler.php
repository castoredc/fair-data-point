<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\PersonsApiResource;
use App\Entity\FAIRData\Person;
use App\Message\Api\Study\GetStudyContactsCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyContactsCommandHandler implements MessageHandlerInterface
{
    public function __invoke(GetStudyContactsCommand $message): PersonsApiResource
    {
        $metadata = $message->getStudy()->getLatestMetadata();
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
