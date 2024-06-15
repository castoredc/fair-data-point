<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\DataSpecification\Common\DataSpecificationModuleApiRequest;
use App\Entity\Enum\ResourceType;
use Symfony\Component\Validator\Constraints as Assert;

class MetadataModelModuleApiRequest extends DataSpecificationModuleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $resourceType;

    protected function parse(): void
    {
        parent::parse();

        $this->resourceType = $this->getFromData('resourceType');
    }

    public function getGroupSequence(): array|Assert\GroupSequence
    {
        return ['MetadataModelModuleApiRequest'];
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::fromString($this->resourceType);
    }
}
