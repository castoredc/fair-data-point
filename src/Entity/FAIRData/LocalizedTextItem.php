<?php


namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class LocalizedTextItem
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
     * @ORM\ManyToOne(targetEntity="LocalizedText", inversedBy="texts",cascade={"persist"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     *
     * @var LocalizedText[]
     */
    private $parent;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Language",cascade={"persist"})
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     *
     * @var Language
     */
    private $language;

    /**
     * LocalizedTextItem constructor.
     * @param string $text
     * @param Language $language
     */
    public function __construct(string $text, Language $language)
    {
        $this->text = $text;
        $this->language = $language;
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
     * @return LocalizedText[]
     */
    public function getParent(): array
    {
        return $this->parent;
    }

    /**
     * @param LocalizedText[] $parent
     */
    public function setParent(array $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function toArray()
    {
        return [
            'text' => $this->text,
            'language' => $this->language->getCode()
        ];
    }
}