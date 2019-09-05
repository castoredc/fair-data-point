<?php


namespace App\Entity\FAIRData;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class LocalizedText
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="LocalizedTextItem", mappedBy="parent", cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="texts", referencedColumnName="id")
     *
     * @var Collection
     */
    private $texts;

    /**
     * LocalizedText constructor.
     * @param ArrayCollection $texts
     */
    public function __construct(ArrayCollection $texts)
    {
        $this->texts = $texts;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Collection
     */
    public function getTexts(): Collection
    {
        return $this->texts;
    }

    /**
     * @param Collection $texts
     */
    public function setTexts(Collection $texts): void
    {
        $this->texts = $texts;
    }

    public function toArray()
    {
        $array = [];

        foreach($this->texts as $text)
        {
            /** @var LocalizedTextItem $text */
            $array[] = $text->toArray();
        }

        return $array;
    }

}