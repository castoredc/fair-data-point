<?php
declare(strict_types=1);

namespace App\Api\Request\Dataset;

use App\Api\Request\SingleApiRequest;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use function boolval;

class DatasetApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @AppAssert\Slug(type="App\Entity\FAIRData\Dataset")
     */
    private string $slug;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $defaultMetadataModel;

    /**
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private bool $published;

    protected function parse(): void
    {
        $this->slug = $this->getFromData('slug');
        $this->defaultMetadataModel = $this->getFromData('defaultMetadataModel');
        $this->published = boolval($this->getFromData('published'));
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDefaultMetadataModel(): string
    {
        return $this->defaultMetadataModel;
    }

    public function getPublished(): bool
    {
        return $this->published;
    }
}
