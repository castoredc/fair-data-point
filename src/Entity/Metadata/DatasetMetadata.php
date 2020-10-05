<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\FAIRData\Dataset;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_dataset")
 * @ORM\HasLifecycleCallbacks
 */
class DatasetMetadata extends Metadata
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Dataset", inversedBy="metadata", fetch="EAGER")
     * @ORM\JoinColumn(name="dataset", referencedColumnName="id", nullable=FALSE)
     */
    private Dataset $dataset;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    public function setDataset(Dataset $dataset): void
    {
        $this->dataset = $dataset;
    }
}
