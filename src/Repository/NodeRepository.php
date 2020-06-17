<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Entity\Enum\NodeType;
use Doctrine\ORM\EntityRepository;
use function assert;

class NodeRepository extends EntityRepository
{
    public function findByModelAndId(DataModel $dataModel, string $nodeId): ?Node
    {
        return $this->findOneBy([
            'dataModel' => $dataModel,
            'id' => $nodeId,
        ]);
    }

    /** @return Node[] */
    public function findNodesByType(DataModel $dataModel, NodeType $type): array
    {
        return $this->createQueryBuilder('node')
                    ->select('node')
                    ->where('node.dataModel = :dataModel')
                    ->andWhere('node INSTANCE OF :type')
                    ->setParameter('dataModel', $dataModel)
                    ->setParameter('type', $this->getEntityManager()->getClassMetadata($type->getClassName()))
                    ->orderBy('node.title', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function findRecordNodeForModel(DataModel $dataModel): ?RecordNode
    {
        $nodes = $this->findNodesByType($dataModel, NodeType::record());
        $node = $nodes[0];
        assert($node instanceof RecordNode);

        return $node;
    }
}
