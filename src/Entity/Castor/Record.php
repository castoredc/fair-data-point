<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Data\RecordDataCollection;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="castor_record")
 */
class Record
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     */
    private $recordId;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CastorStudy", fetch="EAGER")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=FALSE)
     *
     * @var CastorStudy
     */
    private $study;

    /**
     * @ORM\ManyToOne(targetEntity="Institute", fetch="EAGER", inversedBy="records")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="institute_id", referencedColumnName="id", nullable=FALSE),
     *      @ORM\JoinColumn(name="study_id", referencedColumnName="study_id", nullable=FALSE)
     * })
     *
     * @var Institute
     */
    private $institute;

    /** @var RecordDataCollection */
    private $data;

    /** @var DateTimeImmutable */
    private $createdOn;

    /** @var DateTimeImmutable */
    private $updatedOn;

    public function __construct(CastorStudy $study, Institute $institute, string $recordId, DateTimeImmutable $createdOn, DateTimeImmutable $updatedOn)
    {
        $this->study = $study;
        $this->institute = $institute;
        $this->recordId = $recordId;
        $this->createdOn = $createdOn;
        $this->updatedOn = $updatedOn;
    }

    public function getRecordId(): string
    {
        return $this->recordId;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }

    public function getData(): RecordDataCollection
    {
        return $this->data;
    }

    public function setData(RecordDataCollection $data): void
    {
        $this->data = $data;
    }

    public function getId(): string
    {
        return $this->recordId;
    }

    public function getCreatedOn(): DateTimeImmutable
    {
        return $this->createdOn;
    }

    public function getUpdatedOn(): DateTimeImmutable
    {
        return $this->updatedOn;
    }

    public function getInstitute(): Institute
    {
        return $this->institute;
    }
}
