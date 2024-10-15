<?php
declare(strict_types=1);

namespace App\Api\Request\Security;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class UserApiRequest extends SingleApiRequest
{
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

    protected function parse(): void
    {
        $this->firstName = $this->getFromData('firstName');
        $this->middleName = $this->getFromData('middleName');
        $this->lastName = $this->getFromData('lastName');
        $this->email = $this->getFromData('email');
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
}
