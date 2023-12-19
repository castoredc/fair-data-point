<?php
declare(strict_types=1);

namespace App\CommandHandler\Study\Provenance;

use App\Command\Study\Provenance\GetStudyContactsCommand;
use App\Entity\FAIRData\Agent\Person;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetStudyContactsCommandHandler
{
    /** @return Person[] */
    public function __invoke(GetStudyContactsCommand $command): array
    {
        $metadata = $command->getStudy()->getLatestMetadata();

        if ($metadata === null) {
            return [];
        }

        return $metadata->getContacts();
    }
}
