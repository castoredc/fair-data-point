<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;

class DeleteMetadataModelFieldCommand
{
    private MetadataModelField $field;

    public function __construct(MetadataModelField $field)
    {
        $this->field = $field;
    }

    public function getField(): MetadataModelField
    {
        return $this->field;
    }
}
