<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\CreateTripleCommand;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\DataSpecification\DataModel\Predicate;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Entity\Iri;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class CreateTripleCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /** @throws InvalidNodeType */
    public function __invoke(CreateTripleCommand $command): void
    {
        $module = $command->getModule();

        $dataModelVersion = $module->getVersion();
        assert($dataModelVersion instanceof DataModelVersion);

        $dataModel = $dataModelVersion->getDataSpecification();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $dataModel)) {
            throw new NoAccessPermission();
        }

        $nodeRepository = $this->em->getRepository(Node::class);

        if ($command->getSubjectType()->isRecord()) {
            $subject = $nodeRepository->findRecordNodeForModel($dataModelVersion);
        } else {
            $subject = $nodeRepository->findByModelAndId($dataModelVersion, $command->getSubjectValue());
        }

        assert($subject instanceof Node);

        $predicate = new Predicate($dataModelVersion, new Iri($command->getPredicateValue()));

        if ($command->getObjectType()->isRecord()) {
            $object = $nodeRepository->findRecordNodeForModel($dataModelVersion);
        } else {
            $object = $nodeRepository->findByModelAndId($dataModelVersion, $command->getObjectValue());
        }

        assert($object instanceof Node);

        if ($command->getObjectType()->isValue() && $module->isRepeated()) {
            assert($object instanceof ValueNode);

            if (! $object->isRepeated()) {
                throw new InvalidNodeType();
            }
        }

        $triple = new Triple($module, $subject, $predicate, $object);

        $module->addElementGroup($triple);

        $this->em->persist($predicate);
        $this->em->persist($triple);
        $this->em->persist($module);

        $this->em->flush();
    }
}
