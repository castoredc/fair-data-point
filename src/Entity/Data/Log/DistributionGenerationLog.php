<?php
declare(strict_types=1);

namespace App\Entity\Data\Log;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Enum\DistributionGenerationStatus;
use App\Traits\CreatedAt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'log_generation_distribution')]
#[ORM\Entity(repositoryClass: \App\Repository\DistributionGenerationLogRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DistributionGenerationLog
{
    use CreatedAt;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'distribution', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Data\DistributionContents\DistributionContents::class, inversedBy: 'logs', cascade: ['persist'])]
    private DistributionContents $distribution;

    /**
     * @var Collection<DistributionGenerationRecordLog>
     */
    #[ORM\OneToMany(targetEntity: \DistributionGenerationRecordLog::class, mappedBy: 'log', cascade: ['persist'])]
    private Collection $records;

    #[ORM\Column(type: 'DistributionGenerationStatusType')]
    private DistributionGenerationStatus $status;

    /**
     * @var mixed[]|null
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $errors = [];

    public function __construct(DistributionContents $distribution)
    {
        $this->distribution = $distribution;
        $this->records = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
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
