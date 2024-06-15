<?php
declare(strict_types=1);

namespace App\Api\Resource\Language;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Language;

class LanguagesApiResource implements ApiResource
{
    /** @param Language[] $languages */
    public function __construct(private array $languages)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->languages as $language) {
            $data[] = (new LanguageApiResource($language))->toArray();
        }

        return $data;
    }
}
