<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;

class DeleteMetadataModelFormCommand
{
    public function __construct(private MetadataModelForm $form)
    {
    }

    public function getForm(): MetadataModelForm
    {
        return $this->form;
    }
}
