<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\CreateDataModelNodeMappingCommand;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataSpecification\Mapping\ElementMapping;
use App\Entity\Data\DataSpecification\Mapping\Mapping;
use App\Entity\Enum\CastorEntityType;
use App\Entity\Enum\StructureType;
use App\Exception\Distribution\RDF\InvalidSyntax;
use App\Exception\Distribution\RDF\VariableNotSelected;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\UserNotACastorUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function array_key_exists;

#[AsMessageHandler]
class CreateDataModelNodeMappingCommandHandler extends CreateDataModelMappingCommandHandler
{
    /**
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws InvalidEntityType
     * @throws UserNotACastorUser
     * @throws VariableNotSelected
     * @throws InvalidSyntax
     */
    public function __invoke(CreateDataModelNodeMappingCommand $command): Mapping
    {
        parent::setup($command);
        $dataModelVersion = $command->getDataModelVersion();

        $node = $this->em->getRepository(ValueNode::class)->find($command->getNode());

        if ($node === null) {
            throw new NotFound();
        }

        $mapping = $this->study->getMappingByNodeAndVersion($node, $dataModelVersion);

        if ($mapping === null) {
            $mapping = new ElementMapping($this->study, $node, $dataModelVersion);
        }

        if ($command->isTransform()) {
            $elements = [];

            foreach ($command->getElements() as $elementId) {
                $element = $this->entityHelper->getEntityByTypeAndId(
                    $this->study,
                    CastorEntityType::field(),
                    $elementId
                );

                $elements[$element->getSlug()] = $element;
                $this->em->persist($element);
            }

            $variables = $this->dataTransformationService->parseSyntax($command->getTransformSyntax());

            if ($variables === false) {
                throw new InvalidSyntax();
            }

            $selectedEntities = new ArrayCollection();

            foreach ($variables as $variable) {
                if (! array_key_exists($variable, $elements)) {
                    throw new VariableNotSelected($variable);
                }

                $selectedEntities->add($elements[$variable]);
            }

            if ($selectedEntities->count() === 0) {
                throw new InvalidEntityType();
            }

            $mapping->setEntities($selectedEntities);
            $mapping->setTransformData(true);
            $mapping->setSyntax($command->getTransformSyntax());
        } else {
            $element = $this->entityHelper->getEntityByTypeAndId(
                $this->study,
                CastorEntityType::field(),
                $command->getElements()[0]
            );

            if ($node->isRepeated() && $element->getStructureType() === StructureType::study()) {
                throw new InvalidEntityType();
            }

            $mapping->setEntities(new ArrayCollection([$element]));
            $mapping->setTransformData(false);
            $mapping->setSyntax(null);

            $this->em->persist($element);
        }

        $this->em->persist($mapping);
        $this->em->flush();

        return $mapping;
    }
}
