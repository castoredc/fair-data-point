<?php
declare(strict_types=1);

namespace App\Graph\Resource\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\FAIRData\Person;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

class DatasetGraphResource implements GraphResource
{
    private Dataset $dataset;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    public function toGraph(string $baseUrl): Graph
    {
        $graph = new Graph();
        $url = $baseUrl . $this->dataset->getRelativeUrl();
        $metadata = $this->dataset->getLatestMetadata();

        if ($metadata === null) {
            return $graph;
        }

        $graph->addResource($url, 'a', 'dcat:Dataset');

        foreach ($metadata->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($url, 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($url, 'dcterms:hasVersion', $metadata->getVersion()->getValue());

        foreach ($metadata->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        foreach ($metadata->getContacts() as $contactPoint) {
            if (! $contactPoint instanceof Person) {
                continue;
            }

            $graph = (new PersonGraphResource($contactPoint))->addToGraph($baseUrl, $url, 'dcat:contactPoint', $graph);
        }

        // foreach ($metadata->getDepartments() as $department) {
        //     /** @var Department $department */
        //     $graph = (new DepartmentGraphResource($department))->addToGraph($baseUrl, $url, 'dcterms:publisher', $graph);
        // }

        $graph->addResource($url, 'dcterms:language', $metadata->getLanguage()->getAccessUrl());

        if ($metadata->getLicense() !== null) {
            $graph->addResource($url, 'dcterms:license', $metadata->getLicense()->getUrl()->getValue());
        }

        //$graph->addResource($this->getAccessUrl(), 'dcat:theme', $this->theme->getValue());

        foreach ($this->dataset->getDistributions() as $distribution) {
            /** @var Distribution $distribution */
            $graph->addResource($url, 'dcat:distribution', $baseUrl . $distribution->getRelativeUrl());
        }

        return $graph;
    }
}
