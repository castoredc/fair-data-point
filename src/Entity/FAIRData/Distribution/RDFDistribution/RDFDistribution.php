<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\RDFDistribution;

use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Distribution\Distribution;
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
     * @ORM\OneToMany(targetEntity="RDFDistributionModule", mappedBy="distribution",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="modules", referencedColumnName="id")
     * @ORM\OrderBy({"order" = "ASC", "id" = "ASC"})
     *
     * @var Collection<string, RDFDistributionModule>
     */
    private $modules;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $prefix;

    /**
     * @param Collection<string, Agent>                 $publishers
     * @param Collection<string, RDFDistributionModule> $modules
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified, int $accessRights, Collection $modules, string $prefix)
    {
        parent::__construct($slug, $title, $version, $description, $publishers, $language, $license, $issued, $modified, $accessRights);

        $this->modules = $modules;
        $this->prefix = $prefix;
    }

    /**
     * @return Collection<string, RDFDistributionModule>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    /**
     * @param Collection<string, RDFDistributionModule> $modules
     */
    public function setModules(Collection $modules): void
    {
        $this->modules = $modules;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function getRDFUrl(): string
    {
        return parent::getAccessUrl() . '/rdf';
    }

    public function getTwig(): string
    {
        $twig = '';

        foreach ($this->modules as $module) {
            /** @var RDFDistributionModule $module */
            $twig .= '# ' . $module->getTitle() . "\n\n";
            $twig .= $module->getTwig() . "\n\n";
        }

        return $twig;
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
