<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Structure\Step\SurveyStep;
use App\Entity\Enum\StructureType;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Survey extends StructureElement
{
    /** @param SurveyStep[] $surveySteps */
    public function __construct(string $id, CastorStudy $study, ?string $name, array $surveySteps, private ?string $description = null)
    {
        parent::__construct($id, $study, StructureType::survey(), $name);

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

    /** @param array<mixed> $data */
    public static function fromData(array $data, CastorStudy $study): Survey
    {
        $steps = [];
        if (isset($data['survey_steps'])) {
            foreach ($data['survey_steps'] as $step) {
                $steps[] = SurveyStep::fromData($step, $study);
            }
        }

        return new Survey(
            $data['id'],
            $study,
            $data['name'] ?? null,
            $steps,
            $data['description'] ?? null
        );
    }
}
