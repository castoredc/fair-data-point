<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\Terminology\OntologyConcept;

class CreateDatasetMetadataCommand extends CreateMetadataCommand
{
    /**
     * @param Agent[]           $publishers
     * @param OntologyConcept[] $theme
     */
    public function __construct(
        private Dataset $dataset,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate,
        array $publishers,
        private array $theme,
        private ?LocalizedText $keyword = null,
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate, $publishers);
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    /** @return OntologyConcept[] */
    public function getTheme(): array
    {
        return $this->theme;
    }

    public function getKeyword(): ?LocalizedText
    {
        return $this->keyword;
    }
}
