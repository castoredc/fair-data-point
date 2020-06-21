<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Predicate;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use App\Message\Data\UpdateTripleCommand;
use App\Repository\NodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class UpdateTripleCommandHandler implements MessageHandlerInterface
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

    public function __invoke(UpdateTripleCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $triple = $command->getTriple();
        $dataModel = $triple->getModule()->getDataModel();

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

        $triple->setSubject($subject);
        $triple->setPredicate($predicate);
        $triple->setObject($object);

        $this->em->persist($predicate);
        $this->em->persist($triple);

        $this->em->flush();
    }
}
