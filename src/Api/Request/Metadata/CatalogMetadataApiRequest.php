<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Entity\Terminology\OntologyConcept;
use Symfony\Component\Validator\Constraints as Assert;

class CatalogMetadataApiRequest extends MetadataApiRequest
{
    /** @Assert\Type("string") */
    private ?string $homepage = null;

    /** @Assert\Type("string") */
    private ?string $logo = null;

    /**
     * @var mixed[]
     * @Assert\Type("array")
     */
    private array $themeTaxonomy;

    protected function parse(): void
    {
        parent::parse();

        $this->homepage = $this->getFromData('homepage');
        $this->logo = $this->getFromData('logo');
        $this->themeTaxonomy = $this->getFromData('themeTaxonomy');
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @return OntologyConcept[]
     */
    public function getThemeTaxonomy(): array
    {
        $data = [];

        foreach ($this->themeTaxonomy as $theme) {
            $data[] = OntologyConcept::fromData($theme);
        }

        return $data;
    }
}
