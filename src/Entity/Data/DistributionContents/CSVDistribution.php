<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContents;

use App\Entity\DataSpecification\DataDictionary\DataDictionary;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryVersion;
use Doctrine\ORM\Mapping as ORM;
use function assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_csv")
 */
class CSVDistribution extends DistributionContents
{
    public function getDataDictionary(): DataDictionary
    {
        assert($this->dataSpecification instanceof DataDictionary);

        return $this->dataSpecification;
    }

    public function setDataDictionary(DataDictionary $dataDictionary): void
    {
        $this->setDataSpecification($dataDictionary);
    }

    public function getCurrentDataDictionaryVersion(): DataDictionaryVersion
    {
        assert($this->currentDataSpecificationVersion instanceof DataDictionaryVersion);

        return $this->currentDataSpecificationVersion;
    }

    public function setCurrentDataDictionaryVersion(DataDictionaryVersion $currentDataDictionaryVersion): void
    {
        $this->setCurrentDataSpecificationVersion($currentDataDictionaryVersion);
    }

    public function getRelativeUrl(): string
    {
        return $this->getDistribution()->getRelativeUrl() . '/csv';
    }

    public function getType(): string
    {
        return 'csv';
    }

    public function getMediaType(): string
    {
        return 'test/csv';
    }
}
