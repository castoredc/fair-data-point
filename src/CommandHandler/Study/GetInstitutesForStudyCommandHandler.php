<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\GetInstitutesForStudyCommand;
use App\Exception\UserNotACastorUser;
use App\Security\User;
use App\Service\CastorEntityHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Bundle\SecurityBundle\Security;
use function assert;

#[AsMessageHandler]
class GetInstitutesForStudyCommandHandler
{
    private CastorEntityHelper $entityHelper;
    private Security $security;

    public function __construct(CastorEntityHelper $entityHelper, Security $security)
    {
        $this->entityHelper = $entityHelper;
        $this->security = $security;
    }

    /** @throws UserNotACastorUser */
    public function __invoke(GetInstitutesForStudyCommand $command): ArrayCollection
    {
        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->entityHelper->useUser($user->getCastorUser());

        return $this->entityHelper->getInstitutes($command->getStudy());
    }
}
