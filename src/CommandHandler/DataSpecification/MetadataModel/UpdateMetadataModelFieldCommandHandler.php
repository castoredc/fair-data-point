<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelFieldCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Exception\DataSpecification\MetadataModel\NodeAlreadyUsed;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMetadataModelFieldCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    public function __invoke(UpdateMetadataModelFieldCommand $command): void
    {
        $field = $command->getField();
        $form = $field->getForm();
        $metadataModelVersion = $form->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $optionGroupRepository = $this->em->getRepository(MetadataModelOptionGroup::class);
        $nodeRepository = $this->em->getRepository(Node::class);

        $optionGroup = null;

        if ($command->getFieldType()->hasOptionGroup()) {
            $optionGroup = $optionGroupRepository->find($command->getOptionGroup());

            if ($optionGroup === null) {
                throw new NotFound();
            }
        }

        $node = $nodeRepository->find($command->getNode());

        if (! ($node instanceof ValueNode)) {
            throw new NotFound();
        }

        if ($node->hasField() && $node->getField() !== $field) {
            throw new NodeAlreadyUsed($node->getField()->getTitle());
        }

        $form->removeField($field);

        $field->setTitle($command->getTitle());
        $field->setDescription($command->getDescription());
        $field->setOrder($command->getOrder());
        $field->setFieldType($command->getFieldType());
        $field->setOptionGroup($optionGroup);
        $field->setNode($node);
        $field->setResourceType($command->getResourceType());
        $field->setIsRequired($command->getIsRequired());

        $form->addField($field);

        $this->em->persist($field);
        $this->em->persist($form);
        $this->em->flush();
    }
}
