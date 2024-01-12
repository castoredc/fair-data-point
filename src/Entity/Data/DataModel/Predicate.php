<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel;

use App\Entity\Iri;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

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
     * @ORM\Column(type="uuid", length=190)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataModelVersion", inversedBy="predicates", cascade={"persist"})
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     */
    private DataModelVersion $dataModel;

    /** @ORM\Column(type="iri", nullable=false) */
    private Iri $iri;

    public function __construct(DataModelVersion $dataModel, Iri $iri)
    {
        $this->dataModel = $dataModel;
        $this->iri = $iri;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDataModel(): DataModelVersion
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModelVersion $dataModel): void
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
