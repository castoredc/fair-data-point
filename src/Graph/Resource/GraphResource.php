<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\Metadata\Metadata;
use App\Graph\Resource\Agent\Department\DepartmentGraphResource;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use EasyRdf\Graph;
use EasyRdf\Literal;

abstract class GraphResource
{
    protected string $baseUrl;
    protected string $url;

    public function __construct(AccessibleEntity $entity, string $baseUrl)
    {
        $this->url = $entity->getRelativeUrl();
        $this->baseUrl = $baseUrl;
    }

    public function toGraph(): Graph
    {
        return new Graph();
    }

    protected function getIdentifierURL(): string
    {
        return $this->getUrl() . '#identifier';
    }

    protected function addMetadataToGraph(Metadata $metadata, Graph $graph): Graph
    {
        foreach ($metadata->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->getUrl(), 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($this->getUrl(), 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($this->getUrl(), 'dcterms:hasVersion', $metadata->getVersion());

        foreach ($metadata->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->getUrl(), 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph = $this->addAgentsToGraph('dcat:contactPoint', $metadata->getContacts()->toArray(), $graph);
        $graph = $this->addAgentsToGraph('dcterms:publisher', $metadata->getPublishers()->toArray(), $graph);

        $graph->addResource($this->getUrl(), 'dcterms:language', $metadata->getLanguage()->getAccessUrl());

        $graph->addResource($this->getUrl(), 'dcterms:license', $metadata->getLicense()->getUrl()->getValue());

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

        return $graph;
    }

    /** @param Agent[] $agents */
    protected function addAgentsToGraph(string $predicate, array $agents, Graph $graph): Graph
    {
        foreach ($agents as $agent) {
            if ($agent instanceof Department) {
                $graph = (new DepartmentGraphResource($agent, $this->baseUrl))->addToGraph($this->getUrl(), $predicate, $graph);
            }

            if ($agent instanceof Organization) {
                $graph = (new OrganizationGraphResource($agent, $this->baseUrl))->addToGraph($this->getUrl(), $predicate, $graph);
            }

            if (! ($agent instanceof Person)) {
                continue;
            }

            $graph = (new PersonGraphResource($agent, $this->baseUrl))->addToGraph($this->getUrl(), $predicate, $graph);
        }

        return $graph;
    }

    protected function getUrl(): string
    {
        return $this->baseUrl . $this->url;
    }
}
