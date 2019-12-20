<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class LocalizedTextItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="LocalizedText", inversedBy="texts",cascade={"persist"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     *
     * @var LocalizedText|null
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
     * @var Language|null
     */
    private $language;

    public function __construct(string $text, Language $language)
    {
        $this->text = $text;
        $this->language = $language;
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

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'language' => $this->language->getCode(),
        ];
    }
}
