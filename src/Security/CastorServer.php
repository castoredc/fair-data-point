<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use function filter_var;
use const FILTER_VALIDATE_URL;

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

    /** @throws InvalidArgumentException */
    private function __construct(Iri $uri, string $name, string $flag, bool $default = false)
    {
        if (filter_var($uri->getValue(), FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid Castor EDC server URI provided.');
        }

        $this->url = $uri;
        $this->name = $name;
        $this->flag = $flag;
        $this->default = $default;
    }

    /** @throws InvalidArgumentException */
    public static function nonDefaultServer(string $uri, string $name, string $flag): CastorServer
    {
        return new self(new Iri($uri), $name, $flag);
    }

    /** @throws InvalidArgumentException */
    public static function defaultServer(string $uri, string $name, string $flag): CastorServer
    {
        return new self(new Iri($uri), $name, $flag, true);
    }

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
