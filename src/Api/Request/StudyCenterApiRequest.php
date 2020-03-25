<?php

namespace App\Api\Request;

use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Country;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class StudyCenterApiRequest extends GroupedApiRequest
{
    /** @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $name;

    /** @var string
     *
     * @Assert\Country()
     */
    private $country;

    /** @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $city;

    /** @var string|null
     *
     * @Assert\Type("string")
     */
    private $department;

    /** @var string|null
     *
     * @Assert\Type("string")
     */
    private $additionalInformation;

    protected function parse(): void
    {
        $this->name = $this->getFromData('name');
        $this->country = $this->getFromData('country');
        $this->city = $this->getFromData('city');
        $this->department = $this->getFromData('department');
        $this->additionalInformation = $this->getFromData('additionalInformation');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->department;
    }

    /**
     * @return string|null
     */
    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }
}