<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_distribution')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class DistributionMetadata extends Metadata
{
    #[ORM\JoinColumn(name: 'distribution', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Distribution::class, inversedBy: 'metadata')]
    private Distribution $distribution;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;

        $this->values = new ArrayCollection();
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function setDistribution(Distribution $distribution): void
    {
        $this->distribution = $distribution;
    }

    public function getEntity(): ?MetadataEnrichedEntity
    {
        return $this->distribution;
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::distribution();
    }
}
