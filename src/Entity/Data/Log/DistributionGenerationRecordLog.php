<?php
declare(strict_types=1);

namespace App\Entity\Data\Log;

use App\Entity\Castor\Record;
use App\Entity\Enum\DistributionGenerationStatus;
use App\Traits\CreatedAt;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DistributionGenerationRecordLogRepository")
 * @ORM\Table(name="log_generation_distribution_record")
 * @ORM\HasLifecycleCallbacks
 */
class DistributionGenerationRecordLog
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\Record", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="record", referencedColumnName="record_id", nullable=false),
     *     @ORM\JoinColumn(name="study", referencedColumnName="study_id", nullable=false)
     * })
     *
     * @var Record
     */
    private $record;

    /**
     * @ORM\ManyToOne(targetEntity="DistributionGenerationLog", inversedBy="records", cascade={"persist"})
     * @ORM\JoinColumn(name="log", referencedColumnName="id", nullable=false)
     *
     * @var DistributionGenerationLog
     */
    private $log;

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

    public function __construct(Record $record)
    {
        $this->record = $record;
        $this->errors = [];
    }

    public function getId(): string
    {
        return $this->id;
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

    /**
     * @return mixed[]|null
     */
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

    /**
     * @param mixed[] $error
     */
    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }
}
