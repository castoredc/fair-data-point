<?php
declare(strict_types=1);

namespace App\Api\Request\Study\Provenance;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StudyContactApiRequest extends SingleApiRequest
{
    #[Assert\Type('string')]
    private ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $firstName;

    #[Assert\Type('string')]
    private ?string $middleName = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $lastName;

    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[Assert\Type('string')]
    private ?string $orcid = null;

    protected function parse(): void
    {
        $this->id = $this->getFromData('id');
        $this->firstName = $this->getFromData('firstName');
        $this->middleName = $this->getFromData('middleName');
        $this->lastName = $this->getFromData('lastName');
        $this->email = $this->getFromData('email');
        $this->orcid = $this->getFromData('orcid');
    }

    public function getId(): ?string
    {
        return (string) $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
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
