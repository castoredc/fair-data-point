<?php
declare(strict_types=1);

namespace App\Exception;

use function sprintf;

class InvalidMetadataValue extends RenderableApiException
{
    public function __construct(private string $fieldName)
    {
        parent::__construct();
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => sprintf('The value of field \'%s\' is invalid.', $this->fieldName)];
    }
}
