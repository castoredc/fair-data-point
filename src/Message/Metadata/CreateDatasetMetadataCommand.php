<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\LocalizedText;

class CreateDatasetMetadataCommand extends CreateMetadataCommand
{
    private Dataset $dataset;

    /** @param Agent[] $publishers */
    public function __construct(
        Dataset $dataset,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate,
        array $publishers
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate, $publishers);

        $this->dataset = $dataset;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }
}
