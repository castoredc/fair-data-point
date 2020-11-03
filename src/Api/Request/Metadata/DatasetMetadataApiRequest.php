<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Entity\FAIRData\LocalizedText;
use App\Entity\Terminology\OntologyConcept;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class DatasetMetadataApiRequest extends MetadataApiRequest
{
    /**
     * @var mixed[]
     * @Assert\Type("array")
     */
    private array $theme;

    /**
     * @var mixed[]|null
     * @AppAssert\LocalizedText
     */
    private ?array $keyword = null;

    protected function parse(): void
    {
        parent::parse();

        $this->theme = $this->getFromData('theme');
        $this->keyword = $this->getFromData('keyword');
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

    public function getKeyword(): LocalizedText
    {
        return $this->generateLocalizedText($this->keyword);
    }
}
