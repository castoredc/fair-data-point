<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_model_predicate')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Predicate extends CommonPredicate
{
    #[ORM\JoinColumn(name: 'data_model', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \DataModelVersion::class, inversedBy: 'predicates', cascade: ['persist'])]
    private DataModelVersion $dataModel;

    public function __construct(DataModelVersion $dataModel, Iri $iri)
    {
        parent::__construct($iri);

        $this->dataModel = $dataModel;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModelVersion $dataModel): void
    {
        $this->dataModel = $dataModel;
    }

    public function getDataSpecificationVersion(): ModelVersion
    {
        return $this->dataModel;
    }
}
