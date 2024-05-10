<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateNodeCommand;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Iri;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\DataSpecification\Common\Model\InvalidValueType;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateNodeCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /**
     * @throws InvalidNodeType
     * @throws InvalidValueType
     * @throws NoAccessPermission
     */
    public function __invoke(CreateNodeCommand $command): void
    {
        $metadataModelVersion = $command->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $type = $command->getType();

        if ($type->isExternalIri()) {
            $node = new ExternalIriNode($metadataModelVersion, $command->getTitle(), $command->getDescription());
            $node->setIri(new Iri($command->getValue()));
        } elseif ($type->isInternalIri()) {
            $node = new InternalIriNode($metadataModelVersion, $command->getTitle(), $command->getDescription());
            $node->setSlug($command->getValue());
        } elseif ($type->isLiteral()) {
            $node = new LiteralNode($metadataModelVersion, $command->getTitle(), $command->getDescription());
            $node->setValue($command->getValue());
            $node->setDataType($command->getDataType());
        } elseif ($type->isValue()) {
            $node = new ValueNode($metadataModelVersion, $command->getTitle(), $command->getDescription());

            if ($command->getValue() === 'annotated') {
                $node->setIsAnnotatedValue(true);
            } elseif ($command->getValue() === 'plain') {
                $node->setIsAnnotatedValue(false);
                $node->setDataType($command->getDataType());
            } else {
                throw new InvalidValueType();
            }
        } else {
            throw new InvalidNodeType();
        }

        $metadataModelVersion->addElement($node);

        $this->em->persist($node);
        $this->em->persist($metadataModelVersion);

        $this->em->flush();
    }
}
