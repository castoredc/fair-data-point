<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution\RDFDistribution;

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
     *
     * @var RDFDistribution|null
     */

    private $distribution;
    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $twig;

    public function __construct(string $id, string $title, int $order, ?RDFDistribution $distribution, string $twig)
    {
        $this->id = $id;
        $this->title = $title;
        $this->order = $order;
        $this->distribution = $distribution;
        $this->twig = $twig;
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

    public function getDistribution(): ?RDFDistribution
    {
        return $this->distribution;
    }

    public function setDistribution(?RDFDistribution $distribution): void
    {
        $this->distribution = $distribution;
    }

    public function getTwig(): string
    {
        return $this->twig;
    }

    public function setTwig(string $twig): void
    {
        $this->twig = $twig;
    }
}
