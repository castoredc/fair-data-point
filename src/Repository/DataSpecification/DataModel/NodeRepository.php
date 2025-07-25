<?php
declare(strict_types=1);

namespace App\Repository\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Node\RecordNode;
use App\Entity\Enum\NodeType;
use Doctrine\ORM\EntityRepository;
use function assert;

class NodeRepository extends EntityRepository
{
    public function findByModelAndId(DataModelVersion $dataModel, string $nodeId): ?Node
    {
        $node = $this->findOneBy([
            'version' => $dataModel,
            'id' => $nodeId,
        ]);

        assert($node instanceof Node || $node === null);

        return $node;
    }

    /** @return Node[] */
    public function findNodesByType(DataModelVersion $version, NodeType $type): array
    {
        return $this->createQueryBuilder('node')
                    ->select('node')
                    ->where('node.version = :version')
                    ->andWhere('node INSTANCE OF :type')
                    ->setParameter('version', $version)
                    ->setParameter('type', $this->getEntityManager()->getClassMetadata($type->getClassNameForDataModel()))
                    ->orderBy('node.title', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function findRecordNodeForModel(DataModelVersion $dataModel): ?RecordNode
    {
        $nodes = $this->findNodesByType($dataModel, NodeType::record());
        $node = $nodes[0];
        assert($node instanceof RecordNode);

        return $node;
    }
}
