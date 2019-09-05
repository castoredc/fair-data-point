<?php


namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="contact", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
abstract class Contact
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
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

    /**
     * @ORM\ManyToMany(targetEntity="Catalog", mappedBy="publishers",cascade={"persist"})
     *
     * @var Catalog[]
     */
    private $publishedCatalogs;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="publishers",cascade={"persist"})
     *
     * @var Dataset[]
     */
    private $publishedDatasets;

    /**
     * @ORM\ManyToMany(targetEntity="Distribution", mappedBy="publishers",cascade={"persist"})
     *
     * @var Distribution[]
     */
    private $publishedDistributions;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="contactPoint",cascade={"persist"})
     *
     * @var Dataset[]
     */
    private $contactDatasets;

    /**
     * Contact constructor.
     * @param string $slug
     * @param string $name
     */
    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name
        ];
    }
}