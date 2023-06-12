<?php
declare(strict_types=1);

namespace App\Api\Request\Dataset;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;
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
     * @Assert\NotNull()
     * @Assert\Type("bool")
     */
    private bool $published;

    protected function parse(): void
    {
        $this->slug = $this->getFromData('slug');
        $this->published = boolval($this->getFromData('published'));
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPublished(): bool
    {
        return $this->published;
    }
}
