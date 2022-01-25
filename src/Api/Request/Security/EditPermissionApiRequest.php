<?php
declare(strict_types=1);

namespace App\Api\Request\Security;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\PermissionType;
use Symfony\Component\Validator\Constraints as Assert;

class EditPermissionApiRequest extends SingleApiRequest
{
    /** @Assert\Type("string") */
    private string $type;

    protected function parse(): void
    {
        $this->type = $this->getFromData('type');
    }

    public function getType(): PermissionType
    {
        return PermissionType::fromString($this->type);
    }
}
