<?php
declare(strict_types=1);

namespace App\Entity\Data\Log;

use App\Entity\Data\DistributionContents;
use App\Entity\Enum\DistributionGenerationStatus;
use App\Traits\CreatedAt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="log_generation_distribution")
 * @ORM\HasLifecycleCallbacks
 */
class DistributionGenerationLog
{
    use CreatedAt;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DistributionContents", inversedBy="logs", cascade={"persist"})
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id", nullable=false)
     *
     * @var DistributionContents
     */
    private $distribution;

    /**
     * @ORM\OneToMany(targetEntity="DistributionGenerationRecordLog", mappedBy="log", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<DistributionGenerationRecordLog>
     */
    private $records;

    /**
     * @ORM\Column(type="DistributionGenerationStatusType")
     *
     * @var DistributionGenerationStatus
     */
    private $status;

    /**
     * @ORM\Column(type="json", nullable=true)
     *
     * @var mixed[]|null
     */
    private $errors;

    public function __construct(DistributionContents $distribution)
    {
        $this->distribution = $distribution;
        $this->records = new ArrayCollection();
        $this->errors = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDistribution(): DistributionContents
    {
        return $this->distribution;
    }

    /**
     * @return Collection<DistributionGenerationRecordLog>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function getStatus(): DistributionGenerationStatus
    {
        return $this->status;
    }

    /**
     * @return mixed[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setStatus(DistributionGenerationStatus $status): void
    {
        $this->status = $status;
    }

    public function addRecord(DistributionGenerationRecordLog $record): void
    {
        $record->setLog($this);
        $this->records->add($record);
    }

    /**
     * @param mixed[] $error
     */
    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }
}
