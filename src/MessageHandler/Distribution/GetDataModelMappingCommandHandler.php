<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Enum\NodeType;
use App\Entity\PaginatedResultCollection;
use App\Message\Distribution\GetDataModelMappingCommand;
use App\Repository\NodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function assert;
use function count;

class GetDataModelMappingCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @return PaginatedResultCollection<ValueNode> */
    public function __invoke(GetDataModelMappingCommand $command): PaginatedResultCollection
    {
        $distribution = $command->getDistribution();
        $dataModal = $distribution->getDataModel();

        /** @var NodeRepository $repository */
        $repository = $this->em->getRepository(Node::class);

        $valueNodes = $repository->findNodesByType($dataModal, NodeType::value());
        $results = [];

        foreach ($valueNodes as $valueNode) {
            assert($valueNode instanceof ValueNode);
            $mapping = $distribution->getMappingByNode($valueNode);

            $results[] = $mapping ?? $valueNode;
        }

        return new PaginatedResultCollection($results, 1, count($results), count($results));
    }
}
