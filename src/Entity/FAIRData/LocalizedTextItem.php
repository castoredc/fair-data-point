<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="text_localized_item")
 */
class LocalizedTextItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="LocalizedText", inversedBy="texts",cascade={"persist"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private ?LocalizedText $parent = null;

    /** @ORM\Column(type="text") */
    private string $text;

    /**
     * @ORM\ManyToOne(targetEntity="Language",cascade={"persist"})
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     */
    private ?Language $language = null;

    private string $languageCode;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getParent(): LocalizedText
    {
        return $this->parent;
    }

    public function setParent(LocalizedText $parent): void
    {
        $this->parent = $parent;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    /** @return array<string> */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'language' => $this->language->getCode(),
        ];
    }
}
