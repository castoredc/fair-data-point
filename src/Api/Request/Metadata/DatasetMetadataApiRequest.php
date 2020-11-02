<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Entity\Terminology\OntologyConcept;
use Symfony\Component\Validator\Constraints as Assert;

class DatasetMetadataApiRequest extends MetadataApiRequest
{
    /**
     * @var mixed[]
     * @Assert\Type("array")
     */
    private array $theme;

    protected function parse(): void
    {
        parent::parse();

        $this->theme = $this->getFromData('theme');
    }

    /**
     * @return OntologyConcept[]
     */
    public function getTheme(): array
    {
        $data = [];

        foreach ($this->theme as $theme) {
            $data[] = OntologyConcept::fromData($theme);
        }

        return $data;
    }
}
