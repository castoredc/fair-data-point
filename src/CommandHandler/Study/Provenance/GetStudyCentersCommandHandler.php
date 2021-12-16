<?php
declare(strict_types=1);

namespace App\CommandHandler\Study\Provenance;

use App\Command\Study\Provenance\GetStudyCentersCommand;
use App\Entity\Metadata\StudyMetadata\ParticipatingCenter;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyCentersCommandHandler implements MessageHandlerInterface
{
    /**
     * @return ParticipatingCenter[]
     */
    public function __invoke(GetStudyCentersCommand $command): array
    {
        $metadata = $command->getStudy()->getLatestMetadata();

        if ($metadata === null) {
            return [];
        }

        return $metadata->getCenters()->toArray();
    }
}
