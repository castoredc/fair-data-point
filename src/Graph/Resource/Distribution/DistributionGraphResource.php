<?php
declare(strict_types=1);

namespace App\Graph\Resource\Distribution;

use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedTextItem;
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

    public function toGraph(string $baseUrl): EasyRdf_Graph
    {
        $graph = new EasyRdf_Graph();
        $url = $baseUrl . $this->distribution->getRelativeUrl();
        $metadata = $this->distribution->getLatestMetadata();

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

        // foreach ($metadata->getPublishers() as $agent) {
        //     /** @var Department $department */
        //     // $graph = (new DepartmentGraphResource($department))->addToGraph($baseUrl, $url, 'dcterms:publisher', $graph);
        // }

        $graph->addResource($url, 'dcterms:language', $metadata->getLanguage()->getAccessUrl());

        $graph->addResource($url, 'dcterms:license', $metadata->getLicense()->getUrl()->getValue());

        $contents = $this->distribution->getContents();

        if ($contents instanceof RDFDistribution) {
            $graph->addResource($url, 'dcat:downloadURL', $baseUrl . $contents->getRelativeUrl() . '/?download=1');
            $graph->addResource($url, 'dcat:accessURL', $baseUrl . $contents->getRelativeUrl());
            $graph->addLiteral($url, 'dcat:mediaType', 'text/turtle');
        }

        return $graph;
    }
}
