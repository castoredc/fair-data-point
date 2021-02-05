<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\CreateDataModelNodeMappingCommand;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataSpecification\Mapping\ElementMapping;
use App\Entity\Data\DataSpecification\Mapping\Mapping;
use App\Entity\Enum\CastorEntityType;
use App\Entity\Enum\StructureType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\UserNotACastorUser;

class CreateDataModelNodeMappingCommandHandler extends CreateDataModelMappingCommandHandler
{
    /**
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws InvalidEntityType
     * @throws UserNotACastorUser
     */
    public function __invoke(CreateDataModelNodeMappingCommand $command): Mapping
    {
        parent::setup($command);
        $dataModelVersion = $command->getDataModelVersion();

        $node = $this->em->getRepository(ValueNode::class)->find($command->getNode());
        if ($node === null) {
            throw new NotFound();
        }

        if ($command->isTransform()) {
            exit;
        } else {
            $element = $this->entityHelper->getEntityByTypeAndId(
                $this->study,
                CastorEntityType::field(),
                $command->getElements()[0]
            );

            if ($node->isRepeated() && $element->getStructureType() === StructureType::study()) {
                throw new InvalidEntityType();
            }

            if ($this->study->getMappingByNodeAndVersion($node, $dataModelVersion) !== null) {
                $mapping = $this->study->getMappingByNodeAndVersion($node, $dataModelVersion);
                $mapping->setEntity($element);
            } else {
                $mapping = new ElementMapping($this->study, $node, $element, $dataModelVersion);
            }
        }

        $this->em->persist($element);
        $this->em->persist($mapping);
        $this->em->flush();

        return $mapping;
    }
}
