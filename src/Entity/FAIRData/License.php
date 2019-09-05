<?php


namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class License
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
    private $short;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    /**
     * License constructor.
     * @param string $id
     * @param string $slug
     * @param string $short
     * @param string $name
     */
    public function __construct(string $id, string $slug, string $short, string $name)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->short = $short;
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
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
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
    public function getShort(): string
    {
        return $this->short;
    }

    /**
     * @param string $short
     */
    public function setShort(string $short): void
    {
        $this->short = $short;
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
}