<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\FAIRData\Country;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="castor_institute")
 */
class Institute
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\CastorStudy", fetch="EAGER")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=FALSE)
     *
     * @var CastorStudy
     */
    private $study;

    /**
     * @ORM\Column(name="institute_name", type="string", length=1000, nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="abbreviation", type="string", length=1000, nullable=false)
     *
     * @var string
     */
    private $abbreviation;

    /**
     * @ORM\Column(name="code", type="string", length=3, nullable=true)
     *
     * @var string|null
     */
    private $code;

    /** @var int */
    private $countryId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Country",cascade={"persist"})
     * @ORM\JoinColumn(name="country", referencedColumnName="code")
     *
     * @var Country|null
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="Record", mappedBy="institute")
     *
     * @var Collection<Record>
     */
    private $records;

    /** @var bool */
    private $deleted = false;

    public function __construct(
        CastorStudy $study,
        string $id,
        string $name,
        ?string $abbreviation,
        string $code,
        int $countryId,
        bool $deleted
    ) {
        $this->id = $id;
        $this->study = $study;
        $this->name = $name;
        $this->abbreviation = $abbreviation;
        $this->code = $code;
        $this->countryId = $countryId;
        $this->deleted = $deleted;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStudy(): CastorStudy
    {
        return $this->study;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @return Collection<Record>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setCountryId(int $countryId): void
    {
        $this->countryId = $countryId;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @param Collection $records
     */
    public function setRecords(Collection $records): void
    {
        $this->records = $records;
    }
}
