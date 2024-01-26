<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\Model\NamespacePrefix as CommonNamespacePrefix;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_prefix")
 * @ORM\HasLifecycleCallbacks
 */
class NamespacePrefix extends CommonNamespacePrefix
{
    /**
     * @ORM\ManyToOne(targetEntity="MetadataModelVersion", inversedBy="prefixes",cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
    private MetadataModelVersion $metadataModel;

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModel;
    }

    public function setMetadataModelVersion(MetadataModelVersion $metadataModelVersion): void
    {
        $this->metadataModel = $metadataModelVersion;
    }
}
