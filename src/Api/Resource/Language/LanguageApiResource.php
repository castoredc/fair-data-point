<?php
declare(strict_types=1);

namespace App\Api\Resource\Language;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Language;

class LanguageApiResource implements ApiResource
{
    private Language $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'value' => $this->language->getCode(),
            'label' => $this->language->getName(),
        ];
    }
}
