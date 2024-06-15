<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Entity\Enum\ResourceType;

class UpdateMetadataModelFormCommand
{
    public function __construct(private MetadataModelForm $form, private string $title, private int $order, private ResourceType $resourceType)
    {
    }

    public function getForm(): MetadataModelForm
    {
        return $this->form;
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
        return $this->resourceType;
    }
}
