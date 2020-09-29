<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

class CatalogMetadataApiRequest extends MetadataApiRequest
{
    /** @Assert\Type("string") */
    private ?string $homepage = null;

    /** @Assert\Type("string") */
    private ?string $logo = null;

    protected function parse(): void
    {
        parent::parse();

        $this->homepage = $this->getFromData('homepage');
        $this->logo = $this->getFromData('logo');
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
