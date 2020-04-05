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
        $url = $this->dataset->getAccessUrl();
        $baseUrl = $this->dataset->getBaseUrl();
        $metadata = $study->getLatestMetadata();

        if ($metadata === null) {
            return $graph;
        }

        $graph->addResource($url, 'a', 'dcat:Dataset');
        $graph->addLiteral($url, 'dcterms:title', $metadata->getBriefName(), $this->dataset->getLanguage()->getCode());
        $graph->addLiteral($url, 'rdfs:label', $metadata->getBriefName(), $this->dataset->getLanguage()->getCode());
        $graph->addLiteral($url, 'dcterms:hasVersion', $study->getLatestMetadataVersion());

        if ($metadata->getBriefSummary() !== null) {
            $graph->addLiteral($url, 'dcterms:description', $metadata->getBriefSummary(), $this->dataset->getLanguage()->getCode());
        }

        foreach ($metadata->getContacts() as $contactPoint) {
            if (! $contactPoint instanceof Person) {
                continue;
            }

            $graph = (new PersonGraphResource($contactPoint))->addToGraph($baseUrl, $url, 'dcat:contactPoint', $graph);
        }

        foreach ($metadata->getDepartments() as $department) {
            /** @var Department $department */
            $graph = (new DepartmentGraphResource($department))->addToGraph($baseUrl, $url, 'dcterms:publisher', $graph);
        }

        $graph->addResource($url, 'dcterms:language', $this->dataset->getLanguage()->getAccessUrl());

        if ($this->dataset->getLicense() !== null) {
            $graph->addResource($url, 'dcterms:license', $this->dataset->getLicense()->getUrl()->getValue());
        }

        //$graph->addResource($this->getAccessUrl(), 'dcat:theme', $this->theme->getValue());

        foreach ($this->dataset->getDistributions() as $distribution) {
            $graph->addResource($url, 'dcat:distribution', $distribution->getAccessUrl());
        }

        return $graph;
    }
}
