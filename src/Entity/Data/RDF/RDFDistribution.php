<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Data\DistributionContents;
use App\Entity\FAIRData\Distribution;
use Doctrine\Common\Collections\ArrayCollection;
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

    /** @inheritDoc */
    public function __construct(Distribution $distribution, int $accessRights, bool $isPublished)
    {
        parent::__construct($distribution, $accessRights, $isPublished);

        $this->modules = new ArrayCollection();
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

    public function addModule(RDFDistributionModule $module): void
    {
        $order = $module->getOrder();
        $newModules = new ArrayCollection();

        foreach($this->modules as $currentModule) {
            /** @var RDFDistributionModule $currentModule */
            $currentOrder = $currentModule->getOrder();
            $newOrder = $currentOrder >= $order ? ($currentOrder + 1) : $currentOrder;
            $currentModule->setOrder($newOrder);

            $newModules->add($currentModule);
        }

        $newModules->add($module);
        $this->modules = $newModules;
    }

    public function removeModule(RDFDistributionModule $module): void
    {
        $this->modules->remove($module->getId());
    }

    public function getRDFUrl(): string
    {
        return $this->getDistribution()->getAccessUrl() . '/rdf';
    }
}
