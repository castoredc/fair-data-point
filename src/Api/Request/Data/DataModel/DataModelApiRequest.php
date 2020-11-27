<?php
declare(strict_types=1);

namespace App\Api\Request\Data\DataModel;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DataModelApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $title;

    /** @Assert\Type("string") */
    private ?string $description = null;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->description = $this->getFromData('description');
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
