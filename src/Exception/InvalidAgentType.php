<?php
declare(strict_types=1);

namespace App\Exception;

class InvalidAgentType extends RenderableApiException
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'The type of agent should be the same.'];
    }
}
