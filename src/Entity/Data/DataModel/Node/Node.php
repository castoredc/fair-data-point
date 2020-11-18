<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Node;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataSpecification\Element;
use App\Entity\Enum\NodeType;
use Doctrine\ORM\Mapping as ORM;
use function assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodeRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_model_node")
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorMap({
 *     "externalIri" = "ExternalIriNode",
 *     "internalIri" = "InternalIriNode",
 *     "literal" = "LiteralNode",
 *     "record" = "RecordNode",
 *     "value" = "ValueNode",
 * })
 */
abstract class Node extends Element
{
    public function getType(): ?NodeType
    {
        return null;
    }

    public function getValue(): ?string
    {
        return null;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        $version = $this->getVersion();
        assert($version instanceof DataModelVersion);

        return $version;
    }
}
