<?php
declare(strict_types=1);

namespace App\Api\Request\Study;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\StudySource;
use Symfony\Component\Validator\Constraints as Assert;

class StudyApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $source;

    /** @var string|null */
    private $sourceServer;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $sourceId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $catalog;

    /**
     * @var bool|null
     */
    private $published;

    /**
     * @var string|null
     */
    private $slug;

    protected function parse(): void
    {
        $this->source = $this->getFromData('source');
        $this->sourceServer = $this->getFromData('sourceServer') !== null ? (string) $this->getFromData('sourceServer') : null;
        $this->sourceId = $this->getFromData('sourceId');
        $this->name = $this->getFromData('name');
        $this->catalog = $this->getFromData('catalog');
        $this->published = $this->getFromData('published');
        $this->slug = $this->getFromData('slug');
    }

    public function getSource(): StudySource
    {
        return StudySource::fromString($this->source);
    }

    public function getSourceServer(): ?string
    {
        return $this->sourceServer;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCatalog(): ?string
    {
        return $this->catalog;
    }

    public function getPublished(): bool
    {
        return $this->published !== null ? $this->published : false;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
