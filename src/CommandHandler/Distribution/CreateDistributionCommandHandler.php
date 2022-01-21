<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\CreateDistributionCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\License;
use App\Exception\NoAccessPermission;
use App\Security\ApiUser;
use App\Security\User;
use App\Service\DistributionService;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

abstract class CreateDistributionCommandHandler implements MessageHandlerInterface
{
    protected EntityManagerInterface $em;

    protected DistributionService $distributionService;

    protected Security $security;

    protected EncryptionService $encryptionService;

    public function __construct(EntityManagerInterface $em, DistributionService $distributionService, Security $security, EncryptionService $encryptionService)
    {
        $this->em = $em;
        $this->distributionService = $distributionService;
        $this->security = $security;
        $this->encryptionService = $encryptionService;
    }

    protected function handleDistributionCreation(CreateDistributionCommand $command): Distribution
    {
        $dataset = $command->getDataset();
        $user = $this->security->getUser();
        $study = $dataset->getStudy();
        assert($user instanceof User);
        assert($study instanceof CastorStudy);

        if (! $this->security->isGranted('edit', $dataset)) {
            throw new NoAccessPermission();
        }

        $distribution = new Distribution(
            $command->getSlug(),
            $dataset
        );

        $license = $this->em->getRepository(License::class)->find($command->getLicense());
        $distribution->setLicense($license);

        if ($command->getApiUser() !== null && $command->getClientId() !== null && $command->getClientSecret() !== null) {
            $apiUser = new ApiUser($command->getApiUser(), $study->getServer());
            $apiUser->setDecryptedClientId($this->encryptionService, $command->getClientId()->exposeAsString());
            $apiUser->setDecryptedClientSecret($this->encryptionService, $command->getClientSecret()->exposeAsString());

            $this->em->persist($apiUser);

            $distribution->setApiUser($apiUser);
        }

        $distribution->addPermissionForUser($user, PermissionType::manage());

        return $distribution;
    }
}
