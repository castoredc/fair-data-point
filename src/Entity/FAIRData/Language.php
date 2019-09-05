<?php


namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Language
{
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
}