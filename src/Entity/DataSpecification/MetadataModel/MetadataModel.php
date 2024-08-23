<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\DataSpecification;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Study;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataSpecification\MetadataModel\MetadataModelRepository")
 * @ORM\Table(name="metadata_model")
 * @ORM\HasLifecycleCallbacks
 */
class MetadataModel extends DataSpecification
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Catalog", mappedBy="defaultMetadataModel")
     *
     * @var Collection<Catalog>
     */
    private Collection $catalogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Distribution", mappedBy="defaultMetadataModel")
     *
     * @var Collection<Distribution>
     */
    private Collection $distributions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Dataset", mappedBy="defaultMetadataModel")
     *
     * @var Collection<Dataset>
     */
    private Collection $datasets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\FAIRDataPoint", mappedBy="defaultMetadataModel")
     *
     * @var Collection<FAIRDataPoint>
     */
    private Collection $fdps;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Study", mappedBy="defaultMetadataModel")
     *
     * @var Collection<Study>
     */
    private Collection $studies;

    public function __construct(string $title, ?string $description)
    {
        parent::__construct($title, $description);

        $this->catalogs = new ArrayCollection();
        $this->distributions = new ArrayCollection();
        $this->datasets = new ArrayCollection();
        $this->fdps = new ArrayCollection();
        $this->studies = new ArrayCollection();
    }
}
