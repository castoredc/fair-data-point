<?php

namespace App\Entity\Data;

use App\Entity\FAIRData\Distribution;
use App\Security\CastorUser;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_contents")
 * @ORM\HasLifecycleCallbacks
 */
class DistributionContents
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Distribution", inversedBy="contents")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
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
     * @ORM\Column(type="datetime")
     *
     * @var DateTime $created
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     *
     * @var DateTime|null $updated
     */
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorUser")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     *
     * @var CastorUser|null $createdBy
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorUser")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     *
     * @var CastorUser|null $updatedBy
     * @Gedmo\Blameable(on="update")
     */
    private $updatedBy;

    /**
     * DistributionContents constructor.
     *
     * @param Distribution $distribution
     * @param int          $accessRights
     * @param bool         $isPublished
     */
    public function __construct(Distribution $distribution, int $accessRights, bool $isPublished)
    {
        $this->distribution = $distribution;
        $this->accessRights = $accessRights;
        $this->isPublished = $isPublished;
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
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->created = new DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->updated = new DateTime('now');
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function getCreatedBy(): ?CastorUser
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?CastorUser
    {
        return $this->updatedBy;
    }
}