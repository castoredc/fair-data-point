<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\FAIRData\Country;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'castor_institute')]
#[ORM\Entity(repositoryClass: \App\Repository\CastorInstituteRepository::class)]
class Institute
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 190)]
    private string $id;

    #[ORM\JoinColumn(name: 'study_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Castor\CastorStudy::class)]
    private CastorStudy $study;

    #[ORM\Column(name: 'institute_name', type: 'string', length: 1000, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'abbreviation', type: 'string', length: 1000, nullable: false)]
    private string $abbreviation;

    #[ORM\Column(name: 'code', type: 'string', length: 3, nullable: true)]
    private ?string $code = null;

    #[ORM\JoinColumn(name: 'country', referencedColumnName: 'code')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FAIRData\Country::class, cascade: ['persist'])]
    private ?Country $country = null;

    /**
     * @var Collection<Record>
     */
    #[ORM\OneToMany(targetEntity: \Record::class, mappedBy: 'institute')]
    private Collection $records;

    public function __construct(
        CastorStudy $study,
        string $id,
        string $name,
        string $abbreviation,
        ?string $code,
        private int $countryId,
        private bool $deleted = false,
    ) {
        $this->id = $id;
        $this->study = $study;
        $this->name = $name;
        $this->abbreviation = $abbreviation;
        $this->code = $code;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getCountryId(): int
    {
        return $this->countryId;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /** @return Collection<Record> */
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

    /** @param Collection<Record> $records */
    public function setRecords(Collection $records): void
    {
        $this->records = $records;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAbbreviation(string $abbreviation): void
    {
        $this->abbreviation = $abbreviation;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }
}
