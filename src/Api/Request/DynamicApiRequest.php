<?php
declare(strict_types=1);

namespace App\Api\Request;

use Symfony\Component\Validator\Constraints as Assert;

abstract class DynamicApiRequest extends SingleApiRequest
{
    public function getConstraints(): Assert\Collection
    {
        return new Assert\Collection(
            [
                'allowExtraFields' => true,
                'fields' => [],
            ]
        );
    }

    /** @return array<string, mixed> */
    public function getValues(): array
    {
        return [];
    }
}
