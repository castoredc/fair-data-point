<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
class Language
{
    public const ISO_URL = 'http://id.loc.gov/vocabulary/iso639-1/';

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     */
    private string $code;

    /** @ORM\Column(type="string") */
    private string $name;

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAccessUrl(): string
    {
        return self::ISO_URL . $this->getCode();
    }

    /** @return array<string> */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
