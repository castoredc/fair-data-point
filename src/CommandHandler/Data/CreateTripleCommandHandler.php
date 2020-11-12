<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DataModel\Predicate;
use App\Entity\Data\DataModel\Triple;
use App\Entity\Iri;
use App\Exception\InvalidNodeType;
use App\Exception\NoAccessPermission;
use App\Command\Data\CreateTripleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateTripleCommandHandler implements MessageHandlerInterface
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
     */
    public function __invoke(CreateTripleCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $module = $command->getModule();
        $dataModel = $module->getDataModel();

        $nodeRepository = $this->em->getRepository(Node::class);

        if ($command->getSubjectType()->isRecord()) {
            $subject = $nodeRepository->findRecordNodeForModel($dataModel);
        } else {
            $subject = $nodeRepository->findByModelAndId($dataModel, $command->getSubjectValue());
        }

        assert($subject instanceof Node);

        $predicate = new Predicate($dataModel, new Iri($command->getPredicateValue()));

        if ($command->getObjectType()->isRecord()) {
            $object = $nodeRepository->findRecordNodeForModel($dataModel);
        } else {
            $object = $nodeRepository->findByModelAndId($dataModel, $command->getObjectValue());
        }

        assert($object instanceof Node);

        if ($command->getObjectType()->isValue() && $module->isRepeated()) {
            assert($object instanceof ValueNode);

            if (! $object->isRepeated()) {
                throw new InvalidNodeType();
            }
        }

        $triple = new Triple($module, $subject, $predicate, $object);

        $module->addTriple($triple);

        $this->em->persist($predicate);
        $this->em->persist($triple);
        $this->em->persist($module);

        $this->em->flush();
    }
}
