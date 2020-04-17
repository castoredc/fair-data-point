<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\Structure\Step\SurveyStep;

class Survey extends StructureElement
{
    /** @var string|null */
    private $description;

    /**
     * @param SurveyStep[] $surveySteps
     */
    public function __construct(?string $id, ?string $name, ?string $description, array $surveySteps)
    {
        parent::__construct($id, $name);

        $this->description = $description;
        $this->steps = $surveySteps;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Survey
    {
        $steps = [];
        if (isset($data['survey_steps'])) {
            foreach ($data['survey_steps'] as $step) {
                $steps[] = SurveyStep::fromData($step);
            }
        }

        return new Survey(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['description'] ?? null,
            $steps
        );
    }
}
