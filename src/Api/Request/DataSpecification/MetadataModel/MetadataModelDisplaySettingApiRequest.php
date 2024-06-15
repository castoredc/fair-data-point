<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Enum\MetadataDisplayType;
use App\Entity\Enum\ResourceType;
use Symfony\Component\Validator\Constraints as Assert;

class MetadataModelDisplaySettingApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    private int $order;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $title;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $node;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $displayType;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $position;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $resourceType;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->order = $this->getFromData('order');
        $this->node = $this->getFromData('node');
        $this->displayType = $this->getFromData('displayType');
        $this->position = $this->getFromData('position');
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

    public function getNode(): string
    {
        return $this->node;
    }

    public function getDisplayType(): MetadataDisplayType
    {
        return MetadataDisplayType::fromString($this->displayType);
    }

    public function getPosition(): MetadataDisplayPosition
    {
        return MetadataDisplayPosition::fromString($this->position);
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::fromString($this->resourceType);
    }
}
