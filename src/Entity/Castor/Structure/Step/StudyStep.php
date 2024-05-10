<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\StructureType;

class StudyStep extends Step
{
    public function __construct(string $id, CastorStudy $study, ?string $stepDescription, ?string $stepName, ?int $stepOrder, private string $parentId)
    {
        parent::__construct($id, $study, StructureType::study(), $stepDescription, $stepName, $stepOrder);
    }

    public function getParentId(): string
    {
        return $this->parentId;
    }

    public function setParentId(string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data, CastorStudy $study): StudyStep
    {
        return new StudyStep(
            $data['id'],
            $study,
            $data['step_description'] ?? null,
            $data['step_name'] ?? null,
            $data['step_order'] ?? null,
            $data['_embedded']['phase']['phase_id']
        );
    }
}
