<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;

class DeleteMetadataModelFormCommand
{
    private MetadataModelForm $form;

    public function __construct(MetadataModelForm $form)
    {
        $this->form = $form;
    }

    public function getForm(): MetadataModelForm
    {
        return $this->form;
    }
}
