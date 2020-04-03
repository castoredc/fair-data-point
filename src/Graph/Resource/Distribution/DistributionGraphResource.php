<?php

namespace App\Graph\Resource\Distribution;

use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\FAIRData\Distribution\RDFDistribution\RDFDistribution;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Graph\Resource\Agent\Department\DepartmentGraphResource;
use App\Graph\Resource\GraphResource;
use EasyRdf_Graph;

class DistributionGraphResource implements GraphResource
{
    /** @var Distribution */
    private $distribution;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
    }

    public function toGraph(): EasyRdf_Graph
    {
        $graph = new EasyRdf_Graph();
        $metadata = $this->distribution->getDataset()->getStudy()->getLatestMetadata();

        $graph->addResource($this->distribution->getAccessUrl(), 'a', 'dcat:Dataset');

        foreach ($this->distribution->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->distribution->getAccessUrl(), 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($this->distribution->getAccessUrl(), 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($this->distribution->getAccessUrl(), 'dcterms:hasVersion', $this->distribution->getVersion());

        foreach ($this->distribution->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->distribution->getAccessUrl(), 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        foreach ($metadata->getDepartments() as $department) {
            /** @var Department $department */
            $graph = (new DepartmentGraphResource($department))->addToGraph($this->distribution->getAccessUrl(), 'dcterms:publisher', $graph);
        }

        // foreach ($this->distribution->getPublishers() as $publisher) {
        //     /** @var Agent $publisher */
        //     $publisher->addToGraph($this->distribution->getAccessUrl(), 'dcterms:publisher', $graph);
        // }

        $graph->addResource($this->distribution->getAccessUrl(), 'dcterms:language', $this->distribution->getLanguage()->getAccessUrl());

        $graph->addResource($this->distribution->getAccessUrl(), 'dcterms:license', $this->distribution->getLicense()->getUrl()->getValue());

        if($this->distribution instanceof RDFDistribution)
        {
            $graph->addResource($this->distribution->getAccessUrl(), 'dcat:downloadURL', $this->distribution->getRDFUrl() . '/?download=1');
            $graph->addResource($this->distribution->getAccessUrl(), 'dcat:accessURL', $this->distribution->getRDFUrl());
            $graph->addLiteral($this->distribution->getAccessUrl(), 'dcat:mediaType', 'text/turtle');
        }

        return $graph;
    }
}