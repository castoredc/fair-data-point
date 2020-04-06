<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\Step;

class StudyStep extends Step
{
    /** @var string */
    private $parentId;

    public function __construct(?string $id, ?string $stepDescription, ?string $stepName, ?int $stepOrder, string $parentId)
    {
        parent::__construct($id, $stepDescription, $stepName, $stepOrder);

        $this->parentId = $parentId;
    }

    public function getParentId(): string
    {
        return $this->parentId;
    }

    public function setParentId(string $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): StudyStep
    {
        return new StudyStep(
            $data['id'] ?? null,
            $data['step_description'] ?? null,
            $data['step_name'] ?? null,
            $data['step_order'] ?? null,
            $data['_embedded']['phase']['phase_id']
        );
    }
}
