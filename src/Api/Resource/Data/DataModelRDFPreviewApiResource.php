<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;

class DataModelRDFPreviewApiResource implements ApiResource
{
    /** @var DataModelModuleRDFPreviewApiResource[] */
    private $modulePreviews;

    /** @var string */
    private $rdfPreview;

    /** @param DataModelModuleRDFPreviewApiResource[] $modulePreviews */
    public function __construct(array $modulePreviews, string $rdfPreview)
    {
        $this->modulePreviews = $modulePreviews;
        $this->rdfPreview = $rdfPreview;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->modulePreviews as $modulePreview) {
            $data[] = $modulePreview->toArray();
        }

        return [
            'modules' => $data,
            'full' => $this->rdfPreview,
        ];
    }
}
