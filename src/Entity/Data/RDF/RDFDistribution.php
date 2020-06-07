<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DistributionContents;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends DistributionContents
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\DataModel")
     * @ORM\JoinColumn(name="data_model", referencedColumnName="id", nullable=false)
     *
     * @var DataModel
     */
    private $dataModel;

    /**
     * @ORM\OneToMany(targetEntity="DataModelMapping", mappedBy="distribution", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Collection<DataModelMapping>
     */
    private $mappings;

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModel $dataModel): void
    {
        $this->dataModel = $dataModel;
    }

    public function getRDFUrl(): string
    {
        return $this->getDistribution()->getAccessUrl() . '/rdf';
    }

    /**
     * @return Collection<DataModelMapping>
     */
    public function getMappings(): Collection
    {
        return $this->mappings;
    }
}
