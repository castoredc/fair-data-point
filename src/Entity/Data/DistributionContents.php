<?php
declare(strict_types=1);

namespace App\Entity\Data;

use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Distribution;
use App\Entity\Study;
use App\Traits\CreatedAndUpdated;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use function assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_contents")
 * @ORM\HasLifecycleCallbacks
 */
abstract class DistributionContents
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Distribution", inversedBy="contents")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id", nullable=false)
     */
    private Distribution $distribution;

    /**
     * @ORM\Column(name="access", type="DistributionAccessType", nullable=false)
     *
     * @DoctrineAssert\Enum(entity="App\Type\DistributionAccessType")
     */
    private int $accessRights;

    /** @ORM\Column(type="boolean") */
    private bool $isPublished = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\Log\DistributionGenerationLog", mappedBy="distribution", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Collection<DistributionGenerationLog>
     */
    protected Collection $logs;

    public function __construct(Distribution $distribution, int $accessRights, bool $isPublished)
    {
        $this->distribution = $distribution;
        $this->accessRights = $accessRights;
        $this->isPublished = $isPublished;
        $this->logs = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getStudy(): Study
    {
        return $this->getDistribution()->getStudy();
    }

    public function setAccessRights(int $accessRights): void
    {
        $this->accessRights = $accessRights;
    }

    public function getAccessRights(): int
    {
        return $this->accessRights;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return Collection<DistributionGenerationLog>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(DistributionGenerationLog $log): void
    {
        $this->logs->add($log);
    }

    public function getLastGenerationDate(): ?DateTimeImmutable
    {
        if ($this->logs->count() === 0) {
            return null;
        }

        $firstLog = $this->logs->first();
        assert($firstLog instanceof DistributionGenerationLog);

        return $firstLog->getCreatedAt();
    }
}
