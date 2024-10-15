<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_fdp')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class FAIRDataPointMetadata extends Metadata
{
    #[ORM\JoinColumn(name: 'fdp', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: FAIRDataPoint::class, inversedBy: 'metadata')]
    private FAIRDataPoint $fdp;

    public function __construct(FAIRDataPoint $fdp)
    {
        $this->fdp = $fdp;

        $this->values = new ArrayCollection();
    }

    public function getFAIRDataPoint(): FAIRDataPoint
    {
        return $this->fdp;
    }

    public function setFAIRDataPoint(FAIRDataPoint $fdp): void
    {
        $this->fdp = $fdp;
    }

    public function getEntity(): ?MetadataEnrichedEntity
    {
        return $this->fdp;
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::fdp();
    }
}
