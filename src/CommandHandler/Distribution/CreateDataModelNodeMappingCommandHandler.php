<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\CreateDataModelNodeMappingCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Data\DataModel\Mapping\DataModelMapping;
use App\Entity\Data\DataModel\Mapping\DataModelNodeMapping;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Enum\CastorEntityType;
use App\Entity\Enum\StructureType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\UserNotACastorUser;
use App\Security\User;
use App\Service\CastorEntityHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class CreateDataModelNodeMappingCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    private CastorEntityHelper $entityHelper;

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
     * @throws UserNotACastorUser
     */
    public function __invoke(CreateDataModelNodeMappingCommand $command): DataModelMapping
    {
        $contents = $command->getDistribution();
        $distribution = $command->getDistribution()->getDistribution();
        $study = $distribution->getStudy();
        $dataModelVersion = $command->getDataModelVersion();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $user->hasCastorUser()) {
            throw new UserNotACastorUser();
        }

        $this->entityHelper->useUser($user->getCastorUser());

        assert($study instanceof CastorStudy);

        $node = $this->em->getRepository(ValueNode::class)->find($command->getNode());
        if ($node === null) {
            throw new NotFound();
        }

        $element = $this->entityHelper->getEntityByTypeAndId($study, CastorEntityType::field(), $command->getElement());

        if ($node->isRepeated() && $element->getStructureType() === StructureType::study()) {
            throw new InvalidEntityType();
        }

        if ($study->getMappingByNodeAndVersion($node, $dataModelVersion) !== null) {
            $mapping = $study->getMappingByNodeAndVersion($node, $dataModelVersion);
            $mapping->setEntity($element);
        } else {
            $mapping = new DataModelNodeMapping($study, $node, $element, $dataModelVersion);
        }

        $this->em->persist($element);
        $this->em->persist($mapping);
        $this->em->flush();

        return $mapping;
    }
}
