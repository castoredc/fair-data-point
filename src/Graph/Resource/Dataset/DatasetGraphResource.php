<?php
declare(strict_types=1);

namespace App\Graph\Resource\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Person;
use App\Graph\Resource\Agent\Department\DepartmentGraphResource;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use App\Graph\Resource\GraphResource;
use EasyRdf_Graph;

class DatasetGraphResource implements GraphResource
{
    /** @var Dataset */
    private $dataset;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    public function toGraph(): EasyRdf_Graph
    {
        $graph = new EasyRdf_Graph();
        $study = $this->dataset->getStudy();
        $metadata = $study->getLatestMetadata();

        if ($metadata === null) {
            return $graph;
        }

        $graph->addResource($this->dataset->getAccessUrl(), 'a', 'dcat:Dataset');
        $graph->addLiteral($this->dataset->getAccessUrl(), 'dcterms:title', $metadata->getBriefName(), $this->dataset->getLanguage()->getCode());
        $graph->addLiteral($this->dataset->getAccessUrl(), 'rdfs:label', $metadata->getBriefName(), $this->dataset->getLanguage()->getCode());

        $graph->addLiteral($this->dataset->getAccessUrl(), 'dcterms:hasVersion', $study->getLatestMetadataVersion());

        if ($metadata->getBriefSummary() !== null) {
            $graph->addLiteral($this->dataset->getAccessUrl(), 'dcterms:description', $metadata->getBriefSummary(), $this->dataset->getLanguage()->getCode());
        }

        foreach ($metadata->getContacts() as $contactPoint) {
            if (! $contactPoint instanceof Person) {
                continue;
            }

            $graph = (new PersonGraphResource($contactPoint))->addToGraph($this->dataset->getAccessUrl(), 'dcat:contactPoint', $graph);
        }

        foreach ($metadata->getDepartments() as $department) {
            /** @var Department $department */
            $graph = (new DepartmentGraphResource($department))->addToGraph($this->dataset->getAccessUrl(), 'dcterms:publisher', $graph);
        }

        $graph->addResource($this->dataset->getAccessUrl(), 'dcterms:language', $this->dataset->getLanguage()->getAccessUrl());

        if ($this->dataset->getLicense() !== null) {
            $graph->addResource($this->dataset->getAccessUrl(), 'dcterms:license', $this->dataset->getLicense()->getUrl()->getValue());
        }

        //$graph->addResource($this->getAccessUrl(), 'dcat:theme', $this->theme->getValue());

        foreach ($this->dataset->getDistributions() as $distribution) {
            $graph->addResource($this->dataset->getAccessUrl(), 'dcat:distribution', $distribution->getAccessUrl());
        }

        return $graph;
    }
}
