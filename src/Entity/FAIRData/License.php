<?php


namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class License
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     */
    private $slug;

    /**
     * @ORM\Column(type="iri")
     *
     * @var Iri
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    /**
     * License constructor.
     * @param string $slug
     * @param Iri $url
     * @param string $name
     */
    public function __construct(string $slug, Iri $url, string $name)
    {
        $this->slug = $slug;
        $this->url = $url;
        $this->name = $name;
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
     * @return Iri
     */
    public function getUrl(): Iri
    {
        return $this->url;
    }

    /**
     * @param Iri $url
     */
    public function setUrl(Iri $url): void
    {
        $this->url = $url;
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

    public function toArray() {
        return [
            'slug' => $this->slug,
            'url' => $this->url->getValue(),
            'name' => $this->name
        ];
    }
}