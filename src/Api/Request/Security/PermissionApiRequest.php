<?php
declare(strict_types=1);

namespace App\Api\Request\Security;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\PermissionType;
use Symfony\Component\Validator\Constraints as Assert;

class PermissionApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $email;

    /** @Assert\Type("string") */
    private string $type;

    protected function parse(): void
    {
        $this->email = $this->getFromData('email');
        $this->type = $this->getFromData('type');
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): PermissionType
    {
        return PermissionType::fromString($this->type);
    }
}
