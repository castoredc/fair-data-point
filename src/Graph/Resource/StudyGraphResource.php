<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use App\Entity\Study;
use EasyRdf\Graph;

class StudyGraphResource extends GraphResource
{
    public function __construct(private Study $study, string $baseUrl)
    {
        parent::__construct($study, $baseUrl);
    }

    public function toGraph(): Graph
    {
        $graph = new Graph();
        $metadata = $this->study->getLatestMetadata();

        if ($metadata === null) {
            return $graph;
        }

        if ($metadata->getMethodType()->isRegistry()) {
            $graph->addResource($this->getUrl(), 'a', 'ejprd:PatientRegistry');
        }

        $graph->addResource($this->getUrl(), 'dcterms:language', 'http://id.loc.gov/vocabulary/iso639-1/en');

//        $graph->addResource($this->getUrl(), 'dcterms:license', $metadata->getLicense()->getUrl()->getValue());

//        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'a', 'ldp:DirectContainer');
//        $graph->addLiteral($this->baseUrl . Distribution::URL_PATH, 'dcterms:title', 'Datasets');
//        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:hasMemberRelation', 'dcat:dataset');
//        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:membershipResource', $this->getUrl());

        // accessRights

        return $graph;
    }
}
