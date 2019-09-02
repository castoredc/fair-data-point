<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 21/05/2019
 * Time: 10:27
 */

namespace App\Entity;


class RdfChild
{
    /** @var string */
    private $type;

    /** @var string */
    private $value;

    /** @var boolean */
    private $isIri;

    /** @var boolean */
    private $isLocal;

    /**
     * RdfChild constructor.
     * @param string $type
     * @param string $value
     */
    public function __construct(string $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
        $this->isLocal = false;
        $this->isIri = (filter_var($value, FILTER_VALIDATE_URL) !== false);

        if($this->isIri && strpos($value, getenv("FDP_URL")) !== false) $this->isLocal = true;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isIri(): bool
    {
        return $this->isIri;
    }

    /**
     * @param bool $isIri
     */
    public function setIsIri(bool $isIri): void
    {
        $this->isIri = $isIri;
    }

    /**
     * @return bool
     */
    public function isLocal(): bool
    {
        return $this->isLocal;
    }


    public function toArray()
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'isIri' => $this->isIri,
            'isLocal' => $this->isLocal
        ];
    }

}
