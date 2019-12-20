<?php
declare(strict_types=1);

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

    public function __construct(string $slug, Iri $url, string $name)
    {
        $this->slug = $slug;
        $this->url = $url;
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getUrl(): Iri
    {
        return $this->url;
    }

    public function setUrl(Iri $url): void
    {
        $this->url = $url;
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
            'slug' => $this->slug,
            'url' => $this->url->getValue(),
            'name' => $this->name,
        ];
    }
}
