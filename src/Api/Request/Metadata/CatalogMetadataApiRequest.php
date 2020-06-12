<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use Symfony\Component\Validator\Constraints as Assert;

class CatalogMetadataApiRequest extends MetadataApiRequest
{
    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $homepage;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $logo;

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
