<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel;

use App\Entity\DataSpecification\Common\Model\ModelVersion;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_predicate")
 * @ORM\HasLifecycleCallbacks
 */
class Predicate extends CommonPredicate
{
    /**
     * @ORM\ManyToOne(targetEntity="DataModelVersion", inversedBy="predicates", cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
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

    public function getDataSpecification(): ModelVersion
    {
        return $this->dataModel;
    }
}
