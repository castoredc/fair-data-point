<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Data\DataDictionary\DataDictionaryApiResource;
use App\Api\Resource\Data\DataModel\DataModelApiResource;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Distribution;

class DistributionContentApiResource implements ApiResource
{
    private Distribution $distribution;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        if ($this->distribution->getContents() === null) {
            return [];
        }

        $contents = $this->distribution->getContents();

        $data = [
            'id' => $this->distribution->getContents()->getId(),
            'dependencies' => $contents->getDependencies() !== null ? (new DistributionContentsDependencyApiResource($contents->getDependencies()))->toArray() : null,
        ];

        if ($contents instanceof CSVDistribution) {
            $data['dataDictionary'] = (new DataDictionaryApiResource($contents->getDataDictionary()))->toArray();
        }

        if ($contents instanceof RDFDistribution) {
            $data['dataModel'] = (new DataModelApiResource($contents->getDataModel()))->toArray();
        }

        return $data;
    }
}
