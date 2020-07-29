<?php
declare(strict_types=1);

namespace App\Entity\Data;

use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\FAIRData\Distribution;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

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
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Distribution", inversedBy="contents")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id", nullable=false)
     *
     * @var Distribution
     */
    private $distribution;

    /**
     * @ORM\Column(name="access", type="DistributionAccessType", nullable=false)
     *
     * @DoctrineAssert\Enum(entity="App\Type\DistributionAccessType")
     * @var int
     */
    private $accessRights;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isPublished = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\Log\DistributionGenerationLog", mappedBy="distribution", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Collection<DistributionGenerationLog>
     */
    private $logs;

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
}
