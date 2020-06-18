<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\CastorStudy;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\RDF\DataModelMapping;
use App\Entity\Enum\CastorEntityType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Message\Distribution\CreateDataModelMappingCommand;
use App\Service\CastorEntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateDataModelMappingCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    /** @var CastorEntityHelper */
    private $entityHelper;

    public function __construct(EntityManagerInterface $em, Security $security, CastorEntityHelper $entityHelper)
    {
        $this->em = $em;
        $this->security = $security;
        $this->entityHelper = $entityHelper;
    }

    /**
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws InvalidEntityType
     */
    public function __invoke(CreateDataModelMappingCommand $command): DataModelMapping
    {
        $contents = $command->getDistribution();
        $distribution = $command->getDistribution()->getDistribution();
        $study = $distribution->getDataset()->getStudy();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        assert($study instanceof CastorStudy);

        /** @var ValueNode|null $node */
        $node = $this->em->getRepository(ValueNode::class)->find($command->getNode());
        if ($node === null) {
            throw new NotFound();
        }

        $element = $this->entityHelper->getEntityByTypeAndId($study, CastorEntityType::field(), $command->getElement());

        if ($contents->getMappingByNode($node) !== null) {
            $mapping = $contents->getMappingByNode($node);
            $mapping->setEntity($element);
        } else {
            $mapping = new DataModelMapping($contents, $node, $element);
        }

        $this->em->persist($element);
        $this->em->persist($mapping);
        $this->em->flush();

        return $mapping;
    }
}
