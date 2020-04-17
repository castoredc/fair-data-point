<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\StructureCollection\StructureCollection;

class StudyStructureApiResource implements ApiResource
{
    /** @var StructureCollection */
    private $structure;

    public function __construct(StructureCollection $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'study' => (new StructureElementCollectionApiResource($this->structure->getPhases()))->toArray(),
            'survey' => (new StructureElementCollectionApiResource($this->structure->getSurveys()))->toArray(),
            'report' => (new StructureElementCollectionApiResource($this->structure->getReports()))->toArray(),
        ];
    }
}
