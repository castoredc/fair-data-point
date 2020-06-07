<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Predicate;
use App\Entity\Data\DataModel\Triple;
use App\Entity\Iri;
use App\Exception\InvalidNodeType;
use App\Message\Data\CreateTripleCommand;
use App\Repository\NodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function assert;

class CreateTripleCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws InvalidNodeType
     */
    public function __invoke(CreateTripleCommand $command): void
    {
        $module = $command->getModule();
        $dataModel = $module->getDataModel();

        /** @var NodeRepository $nodeRepository */
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

        $triple = new Triple($module, $subject, $predicate, $object);

        $module->addTriple($triple);

        $this->em->persist($predicate);
        $this->em->persist($triple);
        $this->em->persist($module);

        $this->em->flush();
    }
}
