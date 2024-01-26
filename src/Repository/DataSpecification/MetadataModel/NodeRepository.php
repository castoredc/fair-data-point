<?php
declare(strict_types=1);

namespace App\Repository\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\Enum\NodeType;
use Doctrine\ORM\EntityRepository;
use function assert;

class NodeRepository extends EntityRepository
{
    public function findByModelAndId(MetadataModelVersion $metadataModel, string $nodeId): ?Node
    {
        $node = $this->findOneBy([
            'version' => $metadataModel,
            'id' => $nodeId,
        ]);

        assert($node instanceof Node || $node === null);

        return $node;
    }

    /** @return Node[] */
    public function findNodesByType(MetadataModelVersion $version, NodeType $type): array
    {
        return $this->createQueryBuilder('node')
                    ->select('node')
                    ->where('node.version = :version')
                    ->andWhere('node INSTANCE OF :type')
                    ->setParameter('version', $version)
                    ->setParameter('type', $this->getEntityManager()->getClassMetadata($type->getClassName()))
                    ->orderBy('node.title', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function findRecordNodeForModel(MetadataModelVersion $metadataModel): ?RecordNode
    {
        $nodes = $this->findNodesByType($metadataModel, NodeType::record());
        $node = $nodes[0];
        assert($node instanceof RecordNode);

        return $node;
    }
}
