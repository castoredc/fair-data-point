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

    /**
     * @var string|null
     */
    private $sourceServer;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $id;

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

    protected function parse(): void
    {
        $this->source = $this->getFromData('source');
        $this->sourceServer = $this->getFromData('sourceServer');
        $this->id = $this->getFromData('id');
        $this->name = $this->getFromData('name');
        $this->catalog = $this->getFromData('catalog');
    }

    public function getSource(): StudySource
    {
        return StudySource::fromString($this->source);
    }

    public function getSourceServer(): ?string
    {
        return $this->sourceServer !== null ? (string) $this->sourceServer : null;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCatalog(): ?string
    {
        return $this->catalog;
    }
}
