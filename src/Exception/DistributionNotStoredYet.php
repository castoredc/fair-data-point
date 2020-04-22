<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DistributionNotStoredYet extends BadRequestHttpException
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'The distribution is not stored into the database yet.'];
    }
}
