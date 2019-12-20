<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Language
{
    public const ISO_URL = 'http://id.loc.gov/vocabulary/iso639-1/';

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     */
    private $code;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccessUrl(): string
    {
        return self::ISO_URL . $this->getCode();
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
