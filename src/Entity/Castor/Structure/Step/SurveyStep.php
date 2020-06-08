<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

use App\Entity\Castor\Study;
use App\Entity\Enum\StructureType;

class SurveyStep extends Step
{
    public function __construct(string $id, Study $study, ?string $description, ?string $name, ?int $position)
    {
        parent::__construct($id, $study, StructureType::survey(), $description, $name, $position);
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, Study $study): SurveyStep
    {
        return new SurveyStep(
            $data['id'],
            $study,
            $data['description'] ?? null,
            $data['name'] ?? null,
            $data['number'] ?? null
        );
    }
}
