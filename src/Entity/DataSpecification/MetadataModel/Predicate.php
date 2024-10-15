<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_model_predicate')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Predicate extends CommonPredicate
{
    #[ORM\JoinColumn(name: 'metadata_model', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: MetadataModelVersion::class, inversedBy: 'predicates', cascade: ['persist'])]
    private MetadataModelVersion $metadataModel;

    public function __construct(MetadataModelVersion $metadataModelVersion, Iri $iri)
    {
        parent::__construct($iri);

        $this->metadataModel = $metadataModelVersion;
    }

    public function getMetadataModel(): MetadataModelVersion
    {
        return $this->metadataModel;
    }

    public function setMetadataModel(MetadataModelVersion $metadataModel): void
    {
        $this->metadataModel = $metadataModel;
    }

    public function getDataSpecificationVersion(): ModelVersion
    {
        return $this->metadataModel;
    }
}
