<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\Terminology\OntologyConcept;

class CreateDatasetMetadataCommand extends CreateMetadataCommand
{
    private Dataset $dataset;

    /** @var OntologyConcept[] */
    private array $theme;

    /**
     * @param Agent[]           $publishers
     * @param OntologyConcept[] $theme
     */
    public function __construct(
        Dataset $dataset,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate,
        array $publishers,
        array $theme
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate, $publishers);

        $this->dataset = $dataset;
        $this->theme = $theme;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    /**
     * @return OntologyConcept[]
     */
    public function getTheme(): array
    {
        return $this->theme;
    }
}
