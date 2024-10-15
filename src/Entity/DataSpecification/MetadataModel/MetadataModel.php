<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\DataSpecification;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Study;
use App\Repository\DataSpecification\MetadataModel\MetadataModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_model')]
#[ORM\Entity(repositoryClass: MetadataModelRepository::class)]
#[ORM\HasLifecycleCallbacks]
class MetadataModel extends DataSpecification
{
    /** @var Collection<Catalog> */
    #[ORM\OneToMany(targetEntity: Catalog::class, mappedBy: 'defaultMetadataModel')]
    private Collection $catalogs;

    /** @var Collection<Distribution> */
    #[ORM\OneToMany(targetEntity: Distribution::class, mappedBy: 'defaultMetadataModel')]
    private Collection $distributions;

    /** @var Collection<Dataset> */
    #[ORM\OneToMany(targetEntity: Dataset::class, mappedBy: 'defaultMetadataModel')]
    private Collection $datasets;

    /** @var Collection<FAIRDataPoint> */
    #[ORM\OneToMany(targetEntity: FAIRDataPoint::class, mappedBy: 'defaultMetadataModel')]
    private Collection $fdps;

    /** @var Collection<Study> */
    #[ORM\OneToMany(targetEntity: Study::class, mappedBy: 'defaultMetadataModel')]
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
