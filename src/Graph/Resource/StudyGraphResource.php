<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\Study;
use EasyRdf\Graph;
use EasyRdf\Literal;

class StudyGraphResource extends GraphResource
{
    private Study $study;

    public function __construct(Study $study, string $baseUrl)
    {
        $this->study = $study;
        parent::__construct($study, $baseUrl);
    }

    public function toGraph(): Graph
    {
        $graph = new Graph();
        $metadata = $this->study->getLatestMetadata();

        if ($metadata === null) {
            return $graph;
        }

        $graph->addResource($this->getUrl(), 'a', 'dcat:Resource');

        if ($metadata->getMethodType()->isRegistry()) {
            $graph->addResource($this->getUrl(), 'a', 'ejprd:PatientRegistry');
        }

        $graph->addLiteral($this->getUrl(), 'dcterms:title', $metadata->getBriefName(), 'en');
        $graph->addLiteral($this->getUrl(), 'rdfs:label', $metadata->getBriefName(), 'en');

        $graph->addLiteral($this->getUrl(), 'dcterms:hasVersion', $metadata->getVersion());

        $graph->addLiteral($this->getUrl(), 'dcterms:description', $metadata->getBriefSummary(), 'en');

        $graph = $this->addAgentsToGraph('dcat:contactPoint', $metadata->getContacts(), $graph);
        $graph = $this->addAgentsToGraph('dcterms:publisher', $metadata->getOrganizations(), $graph);

        $graph->addResource($this->getUrl(), 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');

//        $graph->addResource($this->getUrl(), 'dcterms:license', $metadata->getLicense()->getUrl()->getValue());

        $graph->addResource($this->getUrl(), 'fdp:metadataIdentifier', $this->getIdentifierURL());

        $graph->addResource($this->getIdentifierURL(), 'a', 'datacite:Identifier');
        $graph->addResource($this->getIdentifierURL(), 'dcterms:identifier', $this->getUrl());

        $createdAt = new Literal($metadata->getCreatedAt()->format('Y-m-d\TH:i:s'), null, 'xsd:dateTime');

        if ($metadata->getUpdatedAt() === null) {
            $updatedAt = $createdAt;
        } else {
            $updatedAt = new Literal($metadata->getUpdatedAt()->format('Y-m-d\TH:i:s'), null, 'xsd:dateTime');
        }

        $graph->addLiteral($this->getUrl(), 'fdp:metadataIssued', $createdAt);
        $graph->addLiteral($this->getUrl(), 'fdp:metadataModified', $updatedAt);

        foreach ($metadata->getConditions() as $condition) {
            $graph->addResource($this->getUrl(), 'dcat:theme', $condition->getUrl()->getValue());
        }

//        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'a', 'ldp:DirectContainer');
//        $graph->addLiteral($this->baseUrl . Distribution::URL_PATH, 'dcterms:title', 'Datasets');
//        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:hasMemberRelation', 'dcat:dataset');
//        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:membershipResource', $this->getUrl());

        foreach ($this->study->getDatasets() as $dataset) {
            /** @var Dataset $dataset */
            $graph->addResource($this->getUrl(), 'dcat:dataset', $this->baseUrl . $dataset->getRelativeUrl());

//            $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:contains', $this->baseUrl . $dataset->getRelativeUrl());
        }

        foreach ($this->study->getCatalogs() as $catalog) {
            $graph->addResource($this->getUrl(), 'dcterms:isPartOf', $this->baseUrl . $catalog->getRelativeUrl());
        }

        if ($metadata->getKeywords() !== null) {
            foreach ($metadata->getKeywords()->getTexts() as $text) {
                /** @var LocalizedTextItem $text */
                $graph->addLiteral($this->getUrl(), 'dcat:keyword', $text->getText(), $text->getLanguage()->getCode());
            }
        }

        // accessRights

        return $graph;
    }
}
