<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel;

class RenderedMetadataModelForm
{
    /** @param MetadataModelField[] $fields */
    public function __construct(private MetadataModelForm $form, private array $fields)
    {
    }

    public function getForm(): MetadataModelForm
    {
        return $this->form;
    }

    /** @return MetadataModelField[] */
    public function getFields(): array
    {
        return $this->fields;
    }
}
