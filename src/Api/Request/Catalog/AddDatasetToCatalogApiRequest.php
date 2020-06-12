<?php
declare(strict_types=1);

namespace App\Api\Request\Study;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class AddDatasetToCatalogApiRequest extends SingleApiRequest
{
    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $datasetId;

    protected function parse(): void
    {
        $this->datasetId = $this->getFromData('datasetId');
    }

    public function getDatasetId(): string
    {
        return $this->datasetId;
    }
}
