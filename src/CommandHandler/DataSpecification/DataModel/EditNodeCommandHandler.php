<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\EditNodeCommand;
use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\LiteralNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\Iri;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\DataSpecification\Common\Model\InvalidValueType;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EditNodeCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /**
     * @throws InvalidNodeType
     * @throws InvalidValueType
     * @throws NoAccessPermission
     */
    public function __invoke(EditNodeCommand $command): void
    {
        $node = $command->getNode();
        $dataModel = $node->getDataModelVersion()->getDataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $dataModel)) {
            throw new NoAccessPermission();
        }

        $node->setTitle($command->getTitle());
        $node->setDescription($command->getDescription());

        if ($node instanceof ExternalIriNode) {
            $node->setIri(new Iri($command->getValue()));
        } elseif ($node instanceof InternalIriNode) {
            $node->setSlug($command->getValue());
            $node->setIsRepeated($command->isRepeated());
        } elseif ($node instanceof LiteralNode) {
            $node->setValue($command->getValue());
            $node->setDataType($command->getDataType());
        } elseif ($node instanceof ValueNode) {
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

        $this->em->persist($node);

        $this->em->flush();
    }
}
