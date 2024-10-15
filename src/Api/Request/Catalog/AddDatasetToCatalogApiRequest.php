<?php
declare(strict_types=1);

namespace App\Api\Request\Catalog;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class AddDatasetToCatalogApiRequest extends SingleApiRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $datasetId = null;

    protected function parse(): void
    {
        $this->datasetId = $this->getFromData('datasetId');
    }

    public function getDatasetId(): string
    {
        return $this->datasetId;
    }
}
