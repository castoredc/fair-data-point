<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContents;

use App\Entity\Data\DataDictionary\DataDictionary;
use App\Entity\Data\DataDictionary\DataDictionaryVersion;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_csv")
 */
class CSVDistribution extends DistributionContents
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataDictionary\DataDictionary", inversedBy="distributions")
     * @ORM\JoinColumn(name="data_dictionary", referencedColumnName="id", nullable=false)
     */
    private DataDictionary $dataDictionary;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataDictionary\DataDictionaryVersion", inversedBy="distributions")
     * @ORM\JoinColumn(name="data_dictionary_version", referencedColumnName="id", nullable=false)
     */
    private DataDictionaryVersion $currentDataDictionaryVersion;

    public function getDataDictionary(): DataDictionary
    {
        return $this->dataDictionary;
    }

    public function setDataDictionary(DataDictionary $dataDictionary): void
    {
        $this->dataDictionary = $dataDictionary;
    }

    public function getCurrentDataDictionaryVersion(): DataDictionaryVersion
    {
        return $this->currentDataDictionaryVersion;
    }

    public function setCurrentDataDictionaryVersion(DataDictionaryVersion $currentDataDictionaryVersion): void
    {
        $this->currentDataDictionaryVersion = $currentDataDictionaryVersion;
    }

    public function getRelativeUrl(): string
    {
        return $this->getDistribution()->getRelativeUrl() . '/csv';
    }
}
