<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\Model\NamespacePrefix as CommonNamespacePrefix;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_model_prefix')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class NamespacePrefix extends CommonNamespacePrefix
{
    #[ORM\JoinColumn(name: 'metadata_model', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \MetadataModelVersion::class, inversedBy: 'prefixes', cascade: ['persist'])]
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
