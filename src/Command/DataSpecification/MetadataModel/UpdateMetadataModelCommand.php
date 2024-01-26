<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\UpdateModelCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;

class UpdateMetadataModelCommand extends UpdateModelCommand
{
    private MetadataModel $metadataModel;

    public function __construct(MetadataModel $metadataModel, string $title, ?string $description)
    {
        parent::__construct($title, $description);

        $this->metadataModel = $metadataModel;
    }

    public function getMetadataModel(): MetadataModel
    {
        return $this->metadataModel;
    }
}
