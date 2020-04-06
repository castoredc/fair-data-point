<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

class SurveyStep extends Step
{
    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): SurveyStep
    {
        return new SurveyStep(
            $data['id'] ?? null,
            $data['description'] ?? null,
            $data['name'] ?? null,
            $data['number'] ?? null
        );
    }
}
