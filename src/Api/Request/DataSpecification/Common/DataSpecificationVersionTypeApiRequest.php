<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\Common;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\VersionType;
use Symfony\Component\Validator\Constraints as Assert;

class DataSpecificationVersionTypeApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $type;

    protected function parse(): void
    {
        $this->type = $this->getFromData('type');
    }

    public function getVersionType(): VersionType
    {
        return VersionType::fromString($this->type);
    }
}
