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
use App\Exception\NoAccessPermission;
use App\Message\Data\CreateNodeCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateNodeCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

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
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();
        $type = $command->getType();

        if ($type->isExternalIri()) {
            $node = new ExternalIriNode($dataModel, $command->getTitle(), $command->getDescription());
            $node->setIri(new Iri($command->getValue()));
        } elseif ($type->isInternalIri()) {
            $node = new InternalIriNode($dataModel, $command->getTitle(), $command->getDescription());
            $node->setSlug($command->getValue());
            $node->setIsRepeated($command->isRepeated());
        } elseif ($type->isLiteral()) {
            $node = new LiteralNode($dataModel, $command->getTitle(), $command->getDescription());
            $node->setValue($command->getValue());
            $node->setDataType($command->getDataType());
        } elseif ($type->isValue()) {
            $node = new ValueNode($dataModel, $command->getTitle(), $command->getDescription());
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

        $dataModel->addNode($node);

        $this->em->persist($node);
        $this->em->persist($dataModel);

        $this->em->flush();
    }
}
