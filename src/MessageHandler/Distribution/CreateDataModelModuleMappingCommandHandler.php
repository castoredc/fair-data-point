<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\CastorStudy;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\RDF\DataModelMapping;
use App\Entity\Data\RDF\DataModelModuleMapping;
use App\Entity\Enum\CastorEntityType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Message\Distribution\CreateDataModelModuleMappingCommand;
use App\Service\CastorEntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateDataModelModuleMappingCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    /** @var CastorEntityHelper */
    private $entityHelper;

    public function __construct(EntityManagerInterface $em, Security $security, CastorEntityHelper $entityHelper)
    {
        $this->em = $em;
        $this->security = $security;
        $this->entityHelper = $entityHelper;
    }

    /**
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws InvalidEntityType
     */
    public function __invoke(CreateDataModelModuleMappingCommand $command): DataModelMapping
    {
        $contents = $command->getDistribution();
        $distribution = $command->getDistribution()->getDistribution();
        $study = $distribution->getDataset()->getStudy();
        $dataModelVersion = $command->getDataModelVersion();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        assert($study instanceof CastorStudy);

        /** @var DataModelModule|null $module */
        $module = $this->em->getRepository(DataModelModule::class)->find($command->getModule());

        if ($module === null || ! $module->isRepeated()) {
            throw new NotFound();
        }

        $element = $this->entityHelper->getEntityByTypeAndId($study, CastorEntityType::fromString($command->getStructureType()->toString()), $command->getElement());

        if ($contents->getMappingByModuleAndVersion($module, $dataModelVersion) !== null) {
            $mapping = $contents->getMappingByModuleAndVersion($module, $dataModelVersion);
            $mapping->setEntity($element);
        } else {
            $mapping = new DataModelModuleMapping($contents, $module, $element, $dataModelVersion);
        }

        $this->em->persist($element);
        $this->em->persist($mapping);
        $this->em->flush();

        return $mapping;
    }
}
