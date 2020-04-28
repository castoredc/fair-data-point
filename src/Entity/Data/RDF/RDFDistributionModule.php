<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_rdf_modules")
 */
class RDFDistributionModule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="RDFDistribution", inversedBy="modules",cascade={"persist"})
     * @ORM\JoinColumn(name="distribution", referencedColumnName="distribution", nullable=false)
     *
     * @var RDFDistribution
     */

    private $distribution;

    /**
     * @ORM\OneToMany(targetEntity="RDFTriple", mappedBy="module", cascade={"persist"}, fetch="EAGER")
     *
     * @var Collection<string, RDFTriple>
     */
    private $triples;

    public function __construct(string $id, string $title, int $order, RDFDistribution $distribution)
    {
        $this->id = $id;
        $this->title = $title;
        $this->order = $order;
        $this->distribution = $distribution;

        $this->triples = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function setDistribution(RDFDistribution $distribution): void
    {
        $this->distribution = $distribution;
    }

    /**
     * @return Collection<string, RDFTriple>
     */
    public function getTriples(): Collection
    {
        return $this->triples;
    }

    /**
     * @param Collection<string, RDFTriple> $triples
     */
    public function setTriples(Collection $triples): void
    {
        $this->triples = $triples;
    }

    public function addTriple(RDFTriple $triple): void
    {
        $this->triples->add($triple);
    }

    public function removeTriple(RDFTriple $triple): void
    {
        $this->triples->remove($triple->getId());
    }
}
