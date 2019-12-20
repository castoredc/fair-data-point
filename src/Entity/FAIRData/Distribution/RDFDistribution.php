<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution;

use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;
use function array_merge;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends Distribution
{
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $twig;

    /**
     * @param Collection<string, Agent> $publishers
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified, string $twig)
    {
        parent::__construct($slug, $title, $version, $description, $publishers, $language, $license, $issued, $modified);

        $this->twig = $twig;
    }

    public function getTwig(): string
    {
        return $this->twig;
    }

    public function setTwig(string $twig): void
    {
        $this->twig = $twig;
    }

    public function getRDFUrl(): string
    {
        return parent::getAccessUrl() . '/rdf';
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'rdf_url' => $this->getRDFUrl(),
            'download_url' => $this->getRDFUrl() . '/?download=1',
            'access_url' => $this->getRDFUrl(),
        ]);
    }

    public function toGraph(): EasyRdf_Graph
    {
        $graph = parent::toGraph();

        $graph->addResource($this->getAccessUrl(), 'dcat:downloadURL', $this->getRDFUrl() . '/?download=1');
        $graph->addResource($this->getAccessUrl(), 'dcat:accessURL', $this->getRDFUrl());
        $graph->addLiteral($this->getAccessUrl(), 'dcat:mediaType', 'text/turtle');

        return $graph;
    }
}
