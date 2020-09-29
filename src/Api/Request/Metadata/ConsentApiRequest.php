<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;
use function boolval;

class ConsentApiRequest extends SingleApiRequest
{
    /**
     * @Assert\NotNull()
     * @Assert\Type("boolean")
     */
    private bool $publish;

    /**
     * @Assert\NotNull()
     * @Assert\Type("boolean")
     */
    private bool $socialMedia;

    protected function parse(): void
    {
        $this->publish = boolval($this->getFromData('publish'));
        $this->socialMedia = boolval($this->getFromData('socialMedia'));
    }

    public function getPublish(): bool
    {
        return $this->publish;
    }

    public function getSocialMedia(): bool
    {
        return $this->socialMedia;
    }
}
