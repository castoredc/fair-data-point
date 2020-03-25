<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class StudyCenterApiRequest extends GroupedApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @var string
     * @Assert\Country()
     */
    private $country;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $city;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $department;

    /**
     * @var string|null
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
}
