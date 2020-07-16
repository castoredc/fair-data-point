<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Enum\NodeType;
use App\Entity\PaginatedResultCollection;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\GetDataModelMappingCommand;
use App\Repository\NodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;
use function count;

class GetDataModelMappingCommandHandler implements MessageHandlerInterface
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

    public function __invoke(GetDataModelMappingCommand $command): PaginatedResultCollection
    {
        $distribution = $command->getDistribution();

        if (! $this->security->isGranted('view', $distribution->getDistribution())) {
            throw new NoAccessPermission();
        }

        /** @var NodeRepository $repository */
        $repository = $this->em->getRepository(Node::class);

        $valueNodes = $repository->findNodesByType($command->getDataModelVersion(), NodeType::value());
        $results = [];

        foreach ($valueNodes as $valueNode) {
            assert($valueNode instanceof ValueNode);
            $mapping = $distribution->getMappingByNodeAndVersion($valueNode, $command->getDataModelVersion());

            $results[] = $mapping ?? $valueNode;
        }

        return new PaginatedResultCollection($results, 1, count($results), count($results));
    }
}
