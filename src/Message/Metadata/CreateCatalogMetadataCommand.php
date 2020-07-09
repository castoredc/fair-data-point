<?php
declare(strict_types=1);

namespace App\Message\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\LocalizedText;

class CreateCatalogMetadataCommand extends CreateMetadataCommand
{
    /** @var Catalog */
    private $catalog;

    /** @var string|null */
    private $homepage;

    /** @var string|null */
    private $logo;

    /** @param Agent[] $publishers */
    public function __construct(
        Catalog $catalog,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate,
        array $publishers,
        ?string $homepage,
        ?string $logo
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate, $publishers);

        $this->catalog = $catalog;
        $this->homepage = $homepage;
        $this->logo = $logo;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }
}
