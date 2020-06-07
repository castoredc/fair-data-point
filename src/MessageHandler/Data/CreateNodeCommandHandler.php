<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\Node\ExternalIriNode;
use App\Entity\Data\DataModel\Node\InternalIriNode;
use App\Entity\Data\DataModel\Node\LiteralNode;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Iri;
use App\Exception\InvalidNodeType;
use App\Exception\InvalidValueType;
use App\Message\Data\CreateNodeCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateNodeCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws InvalidNodeType
     * @throws InvalidValueType
     */
    public function __invoke(CreateNodeCommand $command): void
    {
        $dataModel = $command->getDataModel();
        $type = $command->getType();

        if ($type->isExternalIri()) {
            $node = new ExternalIriNode($dataModel, $command->getTitle(), $command->getDescription());
            $node->setIri(new Iri($command->getValue()));
        } elseif ($type->isInternalIri()) {
            $node = new InternalIriNode($dataModel, $command->getTitle(), $command->getDescription());
            $node->setSlug($command->getValue());
        } elseif ($type->isLiteral()) {
            $node = new LiteralNode($dataModel, $command->getTitle(), $command->getDescription());
            $node->setValue($command->getValue());
            $node->setDataType($command->getDataType());
        } elseif ($type->isValue()) {
            $node = new ValueNode($dataModel, $command->getTitle(), $command->getDescription());

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

        $dataModel->addNode($node);

        $this->em->persist($node);
        $this->em->persist($dataModel);

        $this->em->flush();
    }
}
