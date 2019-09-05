<?php


namespace App\Entity\FAIRData;

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
     * @ORM\OneToMany(targetEntity="LocalizedTextItem", mappedBy="parent",cascade={"persist"})
     * @ORM\JoinColumn(name="texts", referencedColumnName="id")
     *
     * @var LocalizedTextItem[]
     */
    private $texts;

    /**
     * LocalizedText constructor.
     * @param LocalizedTextItem[] $texts
     */
    public function __construct(array $texts)
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
     * @return LocalizedTextItem[]
     */
    public function getTexts(): array
    {
        return $this->texts;
    }

    /**
     * @param LocalizedTextItem[] $texts
     */
    public function setTexts(array $texts): void
    {
        $this->texts = $texts;
    }

}