<?php
declare(strict_types=1);

namespace App\Entity\Data\Log;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Enum\DistributionGenerationStatus;
use App\Traits\CreatedAt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DistributionGenerationLogRepository")
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
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DistributionContents\DistributionContents", inversedBy="logs", cascade={"persist"})
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id", nullable=false)
     */
    private DistributionContents $distribution;

    /**
     * @ORM\OneToMany(targetEntity="DistributionGenerationRecordLog", mappedBy="log", cascade={"persist"})
     *
     * @var Collection<DistributionGenerationRecordLog>
     */
    private Collection $records;

    /** @ORM\Column(type="DistributionGenerationStatusType") */
    private DistributionGenerationStatus $status;

    /**
     * @ORM\Column(type="json", nullable=true)
     *
     * @var mixed[]|null
     */
    private ?array $errors = null;

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

    /** @return Collection<DistributionGenerationRecordLog> */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function getTotalRecordCount(): int
    {
        return $this->records->count();
    }

    private function getRecordCountByStatus(DistributionGenerationStatus $status): int
    {
        $count = 0;

        foreach ($this->records as $record) {
            if (! $record->getStatus()->isEqualTo($status)) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    public function getSuccessRecordCount(): int
    {
        return $this->getRecordCountByStatus(DistributionGenerationStatus::success());
    }

    public function getNotUpdatedRecordCount(): int
    {
        return $this->getRecordCountByStatus(DistributionGenerationStatus::notUpdated());
    }

    public function getErrorRecordCount(): int
    {
        return $this->getRecordCountByStatus(DistributionGenerationStatus::error());
    }

    public function getStatus(): DistributionGenerationStatus
    {
        return $this->status;
    }

    /** @return mixed[]|null */
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

    /** @param mixed[] $error */
    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }
}
