<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;

class UpdateMetadataModelFormCommand
{
    private MetadataModelForm $form;

    private string $title;

    private int $order;

    public function __construct(MetadataModelForm $form, string $title, int $order)
    {
        $this->form = $form;
        $this->title = $title;
        $this->order = $order;
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
