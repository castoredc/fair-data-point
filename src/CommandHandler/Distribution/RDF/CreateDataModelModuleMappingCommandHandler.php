<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\CreateDataModelModuleMappingCommand;
use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataSpecification\Mapping\GroupMapping;
use App\Entity\Data\DataSpecification\Mapping\Mapping;
use App\Entity\Enum\CastorEntityType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\UserNotACastorUser;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDataModelModuleMappingCommandHandler extends CreateDataModelMappingCommandHandler
{
    /**
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws InvalidEntityType
     * @throws UserNotACastorUser
     */
    public function __invoke(CreateDataModelModuleMappingCommand $command): Mapping
    {
        parent::setup($command);
        $dataModelVersion = $command->getDataModelVersion();

        $module = $this->em->getRepository(DataModelGroup::class)->find($command->getModule());

        if ($module === null || ! $module->isRepeated()) {
            throw new NotFound();
        }

        $element = $this->entityHelper->getEntityByTypeAndId(
            $this->study,
            CastorEntityType::fromString($command->getStructureType()->toString()),
            $command->getElement()
        );

        if ($this->study->getMappingByModuleAndVersion($module, $dataModelVersion) !== null) {
            $mapping = $this->study->getMappingByModuleAndVersion($module, $dataModelVersion);
            $mapping->setEntity($element);
        } else {
            $mapping = new GroupMapping($this->study, $module, $element, $dataModelVersion);
        }

        $this->em->persist($element);
        $this->em->persist($mapping);
        $this->em->flush();

        return $mapping;
    }
}
