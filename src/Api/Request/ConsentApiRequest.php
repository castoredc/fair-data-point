<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ConsentApiRequest extends SingleApiRequest
{
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("boolean")
     */
    private $publish;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type("boolean")
     */
    private $socialMedia;

    protected function parse(): void
    {
        $this->publish = $this->getFromData('publish');
        $this->socialMedia = $this->getFromData('socialMedia');
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
