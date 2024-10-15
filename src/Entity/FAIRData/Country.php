<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'country')]
#[ORM\Index(name: 'castorCountryId', columns: ['castor_country_id'])]
#[ORM\Entity(repositoryClass: \App\Repository\CountryRepository::class)]
class Country implements AccessibleEntity
{
    /**
     *
     * @var string
     *
     * Two-letter country abbreviation
     */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 190)]
    private string $code;

    /**
     * @var string
     *
     * Unique identifier of the Country in Castor
     */
    #[ORM\Column(type: 'string')]
    private string $castorCountryId;

    /**
     * @var string
     *
     * Three-letter country abbreviation
     */
    #[ORM\Column(type: 'string')]
    private string $abbreviation;

    /**
     * @var string
     *
     * Top level domain name for country
     */
    #[ORM\Column(type: 'string')]
    private string $tld;

    #[ORM\Column(type: 'string')]
    private string $name;

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCastorCountryId(): string
    {
        return $this->castorCountryId;
    }

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    public function getTld(): string
    {
        return $this->tld;
    }

    public function getRelativeUrl(): string
    {
        return '/fdp/country/' . $this->getCode();
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setCastorCountryId(string $castorCountryId): void
    {
        $this->castorCountryId = $castorCountryId;
    }

    public function setAbbreviation(string $abbreviation): void
    {
        $this->abbreviation = $abbreviation;
    }

    public function setTld(string $tld): void
    {
        $this->tld = $tld;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
