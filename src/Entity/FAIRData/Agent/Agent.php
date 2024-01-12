<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Agent;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="agent", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
abstract class Agent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private ?string $id = null;

    /** @ORM\Column(type="string", unique=true) */
    private string $slug;

    /** @ORM\Column(type="string") */
    private string $name;

    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function hasId(): bool
    {
        return $this->id !== null;
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

    public function getRelativeUrl(): string
    {
        return '/fdp/agent/' . $this->getSlug();
    }
}
