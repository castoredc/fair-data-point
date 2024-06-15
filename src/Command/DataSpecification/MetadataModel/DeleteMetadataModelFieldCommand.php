<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;

class DeleteMetadataModelFieldCommand
{
    public function __construct(private MetadataModelField $field)
    {
    }

    public function getField(): MetadataModelField
    {
        return $this->field;
    }
}
