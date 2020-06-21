<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelModule;

class DataModelModuleRDFPreviewApiResource implements ApiResource
{
    /** @var DataModelModule */
    private $module;

    /** @var string */
    private $rdfPreview;

    public function __construct(DataModelModule $module, string $rdfPreview)
    {
        $this->module = $module;
        $this->rdfPreview = $rdfPreview;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->module->getId(),
            'title' => $this->module->getTitle(),
            'order' => $this->module->getOrder(),
            'rdf' => $this->rdfPreview,
        ];
    }
}
