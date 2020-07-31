<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ORM\Table(name="country", indexes={@ORM\Index(name="castorCountryId", columns={"castor_country_id"})})
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     *
     * Two-letter country abbreviation
     */
    private $code;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     *
     * Unique identifier of the Country in Castor
     */
    private $castorCountryId;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     *
     * Three-letter country abbreviation
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     *
     * Top level domain name for country
     */
    private $tld;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

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
}
