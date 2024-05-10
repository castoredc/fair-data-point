<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelFieldCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class CreateMetadataModelFieldCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    public function __invoke(CreateMetadataModelFieldCommand $command): void
    {
        $form = $command->getForm();
        $metadataModelVersion = $form->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

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

        if ($node === null) {
            throw new NotFound();
        }

        assert($node instanceof ValueNode);

        $field = new MetadataModelField(
            $command->getTitle(),
            $command->getDescription(),
            $command->getOrder(),
            $node,
            $command->getFieldType(),
            $optionGroup,
            [],
            $form
        );

        $form->addField($field);

        $this->em->persist($field);
        $this->em->persist($form);

        $this->em->flush();
    }
}
