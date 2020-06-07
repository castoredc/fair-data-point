<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Iri;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_model_predicate")
 * @ORM\HasLifecycleCallbacks
 */
class Predicate
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataModel", inversedBy="predicates", cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     *
     * @var DataModel
     */
    private $dataModel;

    /**
     * @ORM\Column(type="iri", nullable=false)
     *
     * @var Iri
     */
    private $iri;

    public function __construct(DataModel $dataModel, Iri $iri)
    {
        $this->dataModel = $dataModel;
        $this->iri = $iri;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModel $dataModel): void
    {
        $this->dataModel = $dataModel;
    }

    public function getIri(): Iri
    {
        return $this->iri;
    }

    public function setIri(Iri $iri): void
    {
        $this->iri = $iri;
    }
}
