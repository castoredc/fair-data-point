<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;

class MetadataModelFormsApiResource implements ApiResource
{
    public function __construct(private MetadataModelVersion $metadataModel)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->metadataModel->getForms() as $form) {
            $data[] = (new MetadataModelFormApiResource($form))->toArray();
        }

        return $data;
    }
}
