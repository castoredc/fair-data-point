<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

class ReportStep extends Step
{
    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): ReportStep
    {
        return new ReportStep(
            $data['id'] ?? null,
            $data['report_step_description'] ?? null,
            $data['report_step_name'] ?? null,
            $data['report_step_number'] ?? null
        );
    }
}
