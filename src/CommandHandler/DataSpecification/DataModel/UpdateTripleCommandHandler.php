<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\UpdateTripleCommand;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Predicate;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class UpdateTripleCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(UpdateTripleCommand $command): void
    {
        $triple = $command->getTriple();
        $dataModelVersion = $triple->getGroup()->getVersion();
        assert($dataModelVersion instanceof DataModelVersion);

        $dataModel = $dataModelVersion->getDataModel();

        if (! $this->security->isGranted('edit', $dataModel)) {
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

        $triple->setSubject($subject);
        $triple->setPredicate($predicate);
        $triple->setObject($object);

        $this->em->persist($predicate);
        $this->em->persist($triple);

        $this->em->flush();
    }
}
