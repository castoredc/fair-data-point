<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass="App\Repository\CastorServerRepository") */
class CastorServer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private int $id;

    /** @ORM\Column(type="iri") */
    private Iri $url;

    /** @ORM\Column(type="string", length=255) */
    private string $name;

    /** @ORM\Column(type="string", length=255) */
    private string $flag;

    /** @ORM\Column(type="boolean") */
    private bool $default;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): Iri
    {
        return $this->url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFlag(): string
    {
        return $this->flag;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }
}
