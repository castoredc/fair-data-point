<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateTripleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Predicate;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
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
        $metadataModelVersion = $triple->getGroup()->getVersion();
        assert($metadataModelVersion instanceof MetadataModelVersion);

        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $nodeRepository = $this->em->getRepository(Node::class);

        $subject = $nodeRepository->findByModelAndId($metadataModelVersion, $command->getSubjectValue());

        assert($subject instanceof Node);

        $predicate = new Predicate($metadataModelVersion, new Iri($command->getPredicateValue()));
        $object = $nodeRepository->findByModelAndId($metadataModelVersion, $command->getObjectValue());

        assert($object instanceof Node);

        $triple->setSubject($subject);
        $triple->setPredicate($predicate);
        $triple->setObject($object);

        $this->em->persist($predicate);
        $this->em->persist($triple);

        $this->em->flush();
    }
}
