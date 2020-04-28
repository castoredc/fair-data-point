<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

use App\Entity\Castor\Study;
use App\Entity\Enum\StructureType;

class ReportStep extends Step
{
    public function __construct(string $id, Study $study, ?string $description, ?string $name, ?int $position)
    {
        parent::__construct($id, $study, StructureType::report(), $description, $name, $position);
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, Study $study): ReportStep
    {
        return new ReportStep(
            $data['id'],
            $study,
            $data['report_step_description'] ?? null,
            $data['report_step_name'] ?? null,
            $data['report_step_number'] ?? null
        );
    }
}
