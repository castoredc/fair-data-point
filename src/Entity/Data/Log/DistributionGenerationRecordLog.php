<?php
declare(strict_types=1);

namespace App\Entity\Data\Log;

use App\Entity\Castor\Record;
use App\Entity\Enum\DistributionGenerationStatus;
use App\Traits\CreatedAt;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'log_generation_distribution_record')]
#[ORM\Entity(repositoryClass: \App\Repository\DistributionGenerationRecordLogRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DistributionGenerationRecordLog
{
    use CreatedAt;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'record', referencedColumnName: 'record_id', nullable: false)]
    #[ORM\JoinColumn(name: 'study', referencedColumnName: 'study_id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Castor\Record::class, cascade: ['persist'])]
    private Record $record;

    #[ORM\JoinColumn(name: 'log', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \DistributionGenerationLog::class, inversedBy: 'records', cascade: ['persist'])]
    private DistributionGenerationLog $log;

    #[ORM\Column(type: 'DistributionGenerationStatusType')]
    private DistributionGenerationStatus $status;

    /**
     * @var mixed[]|null
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $errors = [];

    public function __construct(Record $record)
    {
        $this->record = $record;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function getLog(): DistributionGenerationLog
    {
        return $this->log;
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

    public function setLog(DistributionGenerationLog $log): void
    {
        $this->log = $log;
    }

    public function setStatus(DistributionGenerationStatus $status): void
    {
        $this->status = $status;
    }

    /** @param mixed[] $error */
    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }
}
