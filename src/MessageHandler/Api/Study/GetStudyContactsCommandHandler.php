<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\PersonsApiResource;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Person;
use App\Message\Api\Study\GetStudyContactsCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyContactsCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
