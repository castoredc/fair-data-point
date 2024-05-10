<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\CreateDataModelMappingCommand;
use App\Entity\Castor\CastorStudy;
use App\Exception\NoAccessPermission;
use App\Exception\UserNotACastorUser;
use App\Security\User;
use App\Service\CastorEntityHelper;
use App\Service\DataTransformationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
abstract class CreateDataModelMappingCommandHandler
{
    protected CastorStudy $study;

    public function __construct(protected EntityManagerInterface $em, protected Security $security, protected CastorEntityHelper $entityHelper, protected DataTransformationService $dataTransformationService)
    {
    }

    protected function setup(CreateDataModelMappingCommand $command): void
    {
        $distribution = $command->getDistribution()->getDistribution();
        $study = $distribution->getStudy();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->entityHelper->useUser($user->getCastorUser());

        assert($study instanceof CastorStudy);
        $this->study = $study;
    }
}
