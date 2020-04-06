<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\Structure\Step\SurveyStep;

class Survey extends StructureElement
{
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $description;

    /**
     * @param SurveyStep[] $surveySteps
     */
    public function __construct(?string $id, ?string $name, ?string $description, array $surveySteps)
    {
        parent::__construct();

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->steps = $surveySteps;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
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
