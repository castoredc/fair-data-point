<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Data\DistributionContents;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution_rdf")
 */
class RDFDistribution extends DistributionContents
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
}
