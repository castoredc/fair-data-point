<?php
declare(strict_types=1);

namespace App\Api\Request\DataSpecification\MetadataModel;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class OptionGroupApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $title;

    /** @Assert\Type("string") */
    private ?string $description = null;

    /**
     * @var mixed[]
     * @AppAssert\OptionGroupOptions
     */
    private array $options;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->description = $this->getFromData('description');
        $this->options = $this->getFromData('options');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /** @return array<array{id: string|null, title: string, description: string|null, value: string, order: int|null}> */
    public function getOptions(): array
    {
        return $this->options;
    }
}
