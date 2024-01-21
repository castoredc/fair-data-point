<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Data\RecordDataCollection;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CastorRecordRepository")
 * @ORM\Table(name="castor_record")
 */
class Record
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     */
    private string $recordId;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CastorStudy")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=FALSE)
     *
     * phpcs:disable
     *
     * @var CastorStudy
     */
    private $study;

    /**
     * phpcs:enable
     *
     * @ORM\ManyToOne(targetEntity="Institute", inversedBy="records",cascade={"persist"})
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="institute_id", referencedColumnName="id", nullable=FALSE),
     *      @ORM\JoinColumn(name="study_id", referencedColumnName="study_id", nullable=FALSE)
     * })
     */
    private Institute $institute;

    private ?RecordDataCollection $data = null;

    /** @ORM\Column(type="datetime_immutable") */
    private DateTimeImmutable $createdOn;

    /** @ORM\Column(type="datetime_immutable") */
    private DateTimeImmutable $updatedOn;

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

    public function getData(): ?RecordDataCollection
    {
        return $this->data;
    }

    public function hasData(): bool
    {
        return $this->data !== null;
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

    public function setInstitute(Institute $institute): void
    {
        $this->institute = $institute;
    }

    public function setCreatedOn(DateTimeImmutable $createdOn): void
    {
        $this->createdOn = $createdOn;
    }

    public function setUpdatedOn(DateTimeImmutable $updatedOn): void
    {
        $this->updatedOn = $updatedOn;
    }
}
