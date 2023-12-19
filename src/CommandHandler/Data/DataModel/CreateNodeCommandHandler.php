<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\CreateNodeCommand;
use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Iri;
use App\Exception\InvalidNodeType;
use App\Exception\InvalidValueType;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateNodeCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @throws InvalidNodeType
     * @throws InvalidValueType
     * @throws NoAccessPermission
     */
    public function __invoke(CreateNodeCommand $command): void
    {
        $dataModelVersion = $command->getDataModelVersion();
        $dataModel = $dataModelVersion->getDataModel();

        if (! $this->security->isGranted('edit', $dataModel)) {
            throw new NoAccessPermission();
        }

        $type = $command->getType();

        if ($type->isExternalIri()) {
            $node = new ExternalIriNode($dataModelVersion, $command->getTitle(), $command->getDescription());
            $node->setIri(new Iri($command->getValue()));
        } elseif ($type->isInternalIri()) {
            $node = new InternalIriNode($dataModelVersion, $command->getTitle(), $command->getDescription());
            $node->setSlug($command->getValue());
            $node->setIsRepeated($command->isRepeated());
        } elseif ($type->isLiteral()) {
            $node = new LiteralNode($dataModelVersion, $command->getTitle(), $command->getDescription());
            $node->setValue($command->getValue());
            $node->setDataType($command->getDataType());
        } elseif ($type->isValue()) {
            $node = new ValueNode($dataModelVersion, $command->getTitle(), $command->getDescription());
            $node->setIsRepeated($command->isRepeated());

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

        $dataModelVersion->addElement($node);

        $this->em->persist($node);
        $this->em->persist($dataModelVersion);

        $this->em->flush();
    }
}
