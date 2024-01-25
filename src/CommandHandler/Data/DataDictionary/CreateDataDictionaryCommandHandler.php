<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataDictionary;

use App\Command\Data\DataDictionary\CreateDataDictionaryCommand;
use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDataDictionaryCommandHandler
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateDataDictionaryCommand $command): DataDictionary
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataDictionary = new DataDictionary($command->getTitle(), $command->getDescription());

        $version = new DataDictionaryVersion(new Version(self::DEFAULT_VERSION_NUMBER));
        $dataDictionary->addVersion($version);

        $this->em->persist($dataDictionary);
        $this->em->persist($version);
        $this->em->flush();

        return $dataDictionary;
    }
}
