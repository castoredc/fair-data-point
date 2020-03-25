<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="agent", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
abstract class Agent
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
    private $slug;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }

    public function getAccessUrl(): string
    {
        return '/agent/generic/' . $this->getSlug();
    }

    public function toGraph(): EasyRdf_Graph
    {
        return $this->addToGraph(null, null, new EasyRdf_Graph());
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        $graph->addResource($this->getAccessUrl(), 'a', 'foaf:Agent');
        $graph->addLiteral($this->getAccessUrl(), 'foaf:name', $this->name);

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $this->getAccessUrl());
        }

        return $graph;
    }
}
