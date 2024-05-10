<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;

class UpdateMetadataModelFormCommand
{
    public function __construct(private MetadataModelForm $form, private string $title, private int $order)
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
}
