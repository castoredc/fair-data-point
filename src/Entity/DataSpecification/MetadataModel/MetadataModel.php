<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\DataSpecification;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\FAIRDataPoint;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModel extends DataSpecification
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Catalog", mappedBy="metadataModel")
     *
     * @var Collection<Catalog>
     */
    private Collection $catalogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Distribution", mappedBy="metadataModel")
     *
     * @var Collection<Distribution>
     */
    private Collection $distributions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Dataset", mappedBy="metadataModel")
     *
     * @var Collection<Dataset>
     */
    private Collection $datasets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\FAIRDataPoint", mappedBy="metadataModel")
     *
     * @var Collection<FAIRDataPoint>
     */
    private Collection $fdps;
}
