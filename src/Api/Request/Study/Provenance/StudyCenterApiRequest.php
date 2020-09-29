<?php
declare(strict_types=1);

namespace App\Api\Request\Study\Provenance;

use App\Api\Request\GroupedApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StudyCenterApiRequest extends GroupedApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $name;

    /** @Assert\Country() */
    private string $country;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $city;

    /** @Assert\Type("string") */
    private ?string $department = null;

    /** @Assert\Type("string") */
    private ?string $additionalInformation = null;

    /** @Assert\Type("string") */
    private ?string $coordinatesLatitude = null;

    /** @Assert\Type("string") */
    private ?string $coordinatesLongitude = null;

    protected function parse(): void
    {
        $this->name = $this->getFromData('name');
        $this->country = $this->getFromData('country');
        $this->city = $this->getFromData('city');
        $this->department = $this->getFromData('department');
        $this->additionalInformation = $this->getFromData('additionalInformation');
        $this->coordinatesLatitude = $this->getFromData('coordinatesLatitude');
        $this->coordinatesLongitude = $this->getFromData('coordinatesLongitude');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }

    public function getCoordinatesLatitude(): ?string
    {
        return $this->coordinatesLatitude !== '' ? $this->coordinatesLatitude : null;
    }

    public function getCoordinatesLongitude(): ?string
    {
        return $this->coordinatesLongitude !== '' ? $this->coordinatesLongitude : null;
    }
}
