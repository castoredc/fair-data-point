<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\CreateDataModelCommand;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\RecordNode;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDataModelCommandHandler
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateDataModelCommand $command): DataModel
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = new DataModel($command->getTitle(), $command->getDescription());

        $version = new DataModelVersion(new Version(self::DEFAULT_VERSION_NUMBER));
        $dataModel->addVersion($version);

        $recordNode = new RecordNode($version);
        $version->addNode($recordNode);

        $this->em->persist($dataModel);
        $this->em->persist($version);
        $this->em->persist($recordNode);
        $this->em->flush();

        return $dataModel;
    }
}
