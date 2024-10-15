<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\ResourceType;
use Symfony\Component\Validator\Constraints as Assert;

class MetadataModelFormApiRequest extends SingleApiRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $title;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    private int $order;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $resourceType;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->order = $this->getFromData('order');
        $this->resourceType = $this->getFromData('resourceType');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::fromString($this->resourceType);
    }
}
