<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Triple;
use function array_values;

class GroupedTriplesApiResource implements ApiResource
{
    /** @var DataModelModule */
    private $module;

    public function __construct(DataModelModule $module)
    {
        $this->module = $module;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->module->getTriples() as $triple) {
            /** @var Triple $triple */
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

            $data[$subject->getId()]['predicates'][$predicate->getId()]['objects'][] = (new NodeApiResource($object))->toArray();
        }

        foreach ($data as $subjectId => $subject) {
            $data[$subjectId]['predicates'] = array_values($subject['predicates']);
        }

        $data = array_values($data);

        return $data;
    }
}
