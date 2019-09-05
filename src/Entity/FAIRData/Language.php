<?php


namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Language
{
    const ISO_URL = "http://id.loc.gov/vocabulary/iso639-1/";

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
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

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getAccessUrl()
    {
        return self::ISO_URL . $this->getCode();
    }

    public function toArray() {
        return [
            'code' => $this->code,
            'name' => $this->name
        ];
    }
}