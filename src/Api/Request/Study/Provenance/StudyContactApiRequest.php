<?php
declare(strict_types=1);

namespace App\Api\Request\Study\Provenance;

use App\Api\Request\GroupedApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StudyContactApiRequest extends GroupedApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $firstName;

    /**
     * @var string
     * @Assert\Type("string")
     */
    private $middleName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $lastName;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $orcid;

    protected function parse(): void
    {
        $this->firstName = $this->getFromData('firstName');
        $this->middleName = $this->getFromData('middleName');
        $this->lastName = $this->getFromData('lastName');
        $this->email = $this->getFromData('email');
        $this->orcid = $this->getFromData('orcid');
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getOrcid(): ?string
    {
        return $this->orcid;
    }
}
