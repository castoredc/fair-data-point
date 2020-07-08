<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Entity\Data\DistributionContents;
use App\Entity\FAIRData\AccessibleEntity;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends DistributionContents implements AccessibleEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\DataModel", inversedBy="distributions")
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

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isCached = false;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=TRUE)
     *
     * @var DateTimeImmutable|null
     */
    private $lastImport;

    public function getDataModel(): DataModel
    {
        return $this->dataModel;
    }

    public function setDataModel(DataModel $dataModel): void
    {
        $this->dataModel = $dataModel;
    }

    public function getRelativeUrl(): string
    {
        return $this->getDistribution()->getRelativeUrl() . '/rdf';
    }

    /**
     * @return Collection<DataModelMapping>
     */
    public function getMappings(): Collection
    {
        return $this->mappings;
    }

    public function getMappingByNode(ValueNode $node): ?DataModelMapping
    {
        foreach ($this->mappings as $mapping) {
            /** @var DataModelMapping $mapping */
            if ($mapping->getNode() === $node) {
                return $mapping;
            }
        }

        return null;
    }

    public function isCached(): bool
    {
        return $this->isCached;
    }

    public function setIsCached(bool $isCached): void
    {
        $this->isCached = $isCached;
    }

    public function getLastImport(): ?DateTimeImmutable
    {
        return $this->lastImport;
    }

    public function setLastImport(?DateTimeImmutable $lastImport): void
    {
        $this->lastImport = $lastImport;
    }
}
