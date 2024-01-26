<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Triple;
use function array_merge;
use function array_values;
use function assert;

class GroupedTriplesApiResource implements ApiResource
{
    private DataModelGroup $module;

    public function __construct(DataModelGroup $module)
    {
        $this->module = $module;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->module->getTriples() as $triple) {
            assert($triple instanceof Triple);

            $subject = $triple->getSubject();
            $predicate = $triple->getPredicate();
            $object = $triple->getObject();

            if (! isset($data[$subject->getId()])) {
                $data[$subject->getId()] = (new NodeApiResource($subject))->toArray();
                $data[$subject->getId()]['predicates'] = [];
            }

            if (! isset($data[$subject->getId()]['predicates'][$predicate->getId()])) {
                $data[$subject->getId()]['predicates'][$predicate->getId()] = (new PredicateApiResource($predicate))->toArray();
                $data[$subject->getId()]['predicates'][$predicate->getId()]['objects'] = [];
            }

            $data[$subject->getId()]['predicates'][$predicate->getId()]['objects'][] = array_merge([
                'tripleId' => $triple->getId(),
            ], (new NodeApiResource($object))->toArray());
        }

        foreach ($data as $subjectId => $subject) {
            $data[$subjectId]['predicates'] = array_values($subject['predicates']);
        }

        $data = array_values($data);

        return $data;
    }
}
