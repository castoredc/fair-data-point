<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateTripleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Predicate;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Entity\Iri;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\NoAccessPermission;
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

        $metadataModelVersion = $module->getVersion();
        assert($metadataModelVersion instanceof MetadataModelVersion);

        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $nodeRepository = $this->em->getRepository(Node::class);

        if ($command->getSubjectType()->isRecord()) {
            $subject = $nodeRepository->findRecordNodeForModel($metadataModelVersion);
        } else {
            $subject = $nodeRepository->findByModelAndId($metadataModelVersion, $command->getSubjectValue());
        }

        assert($subject instanceof Node);

        $predicate = new Predicate($metadataModelVersion, new Iri($command->getPredicateValue()));

        if ($command->getObjectType()->isRecord()) {
            $object = $nodeRepository->findRecordNodeForModel($metadataModelVersion);
        } else {
            $object = $nodeRepository->findByModelAndId($metadataModelVersion, $command->getObjectValue());
        }

        assert($object instanceof Node);

        $triple = new Triple($module, $subject, $predicate, $object);

        $module->addElementGroup($triple);

        $this->em->persist($predicate);
        $this->em->persist($triple);
        $this->em->persist($module);

        $this->em->flush();
    }
}
