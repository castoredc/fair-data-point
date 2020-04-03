<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CastorServer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="iri")
     *
     * @var Iri
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $flag;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $default;

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
