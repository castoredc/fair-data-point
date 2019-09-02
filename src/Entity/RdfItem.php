<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 21/05/2019
 * Time: 10:26
 */

namespace App\Entity;


class RdfItem
{

    /** @var Iri */
    private $iri;

    /** @var string */
    private $short;

    /** @var RdfChild[] */
    private $children;

    /**
     * RdfItem constructor.
     * @param Iri $iri
     * @param string $short
     */
    public function __construct(Iri $iri, string $short)
    {
        $this->iri = $iri;
        $this->short = $short;
    }

    /**
     * @return Iri
     */
    public function getIri(): Iri
    {
        return $this->iri;
    }

    /**
     * @param Iri $iri
     */
    public function setIri(Iri $iri): void
    {
        $this->iri = $iri;
    }

    /**
     * @return string
     */
    public function getShort(): string
    {
        return $this->short;
    }

    /**
     * @param string $short
     */
    public function setShort(string $short): void
    {
        $this->short = $short;
    }

    /**
     * @return RdfChild[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param RdfChild[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function childrenFromData(array $children)
    {
        foreach($children as $child)
        {
            $this->children[] = new RdfChild(
                $child['type'],
                $child['value']
            );
        }
    }

    public function toArray()
    {
        $children = [];

        foreach($this->children as $child)
        {
            $children[] = $child->toArray();
        }

        return [
            'iri' => $this->iri->getValue(),
            'short' => $this->short,
            'children' => $children
        ];
    }

}
