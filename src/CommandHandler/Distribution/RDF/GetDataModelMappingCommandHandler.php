<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\GetDataModelMappingCommand;
use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Enum\NodeType;
use App\Entity\PaginatedResultCollection;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;
use function count;

#[AsMessageHandler]
class GetDataModelMappingCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(GetDataModelMappingCommand $command): PaginatedResultCollection
    {
        $distribution = $command->getDistribution();
        $study = $distribution->getStudy();

        if (! $this->security->isGranted('view', $distribution->getDistribution())) {
            throw new NoAccessPermission();
        }

        $results = [];

        if ($command->getType()->isNode()) {
            $repository = $this->em->getRepository(Node::class);

            $valueNodes = $repository->findNodesByType($command->getDataModelVersion(), NodeType::value());

            foreach ($valueNodes as $valueNode) {
                assert($valueNode instanceof ValueNode);
                $mapping = $study->getMappingByNodeAndVersion($valueNode, $command->getDataModelVersion());

                $results[] = $mapping ?? $valueNode;
            }
        } elseif ($command->getType()->isModule()) {
            $repeatedModules = $command->getDataModelVersion()->getRepeatedGroups();

            foreach ($repeatedModules as $repeatedModule) {
                assert($repeatedModule instanceof DataModelGroup);
                $mapping = $study->getMappingByModuleAndVersion($repeatedModule, $command->getDataModelVersion());

                $results[] = $mapping ?? $repeatedModule;
            }
        }

        return new PaginatedResultCollection(
            $results,
            1,
            count($results),
            count($results)
        );
    }
}
