<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 20/05/2019
 * Time: 23:25
 */

namespace App\Entity;


class Iri
{
    /** @var string */
    private $value;

    private static $prefixes = [
        'http://id.loc.gov/vocabulary/iso639-1/' => 'lang',
        'http://purl.org/dc/terms/' => 'dcterms',
        'http://www.re3data.org/schema/3-0#' => 'r3d'
    ];

    public function __construct(string $uri)
    {
        $this->value = $uri;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getBase(): string
    {
        return basename($this->value);
    }
}