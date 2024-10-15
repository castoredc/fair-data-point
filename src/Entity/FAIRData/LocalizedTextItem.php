<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'text_localized_item')]
#[ORM\Entity]
class LocalizedTextItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'parent', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \LocalizedText::class, inversedBy: 'texts', cascade: ['persist'])]
    private ?LocalizedText $parent = null;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\JoinColumn(name: 'language', referencedColumnName: 'code')]
    #[ORM\ManyToOne(targetEntity: \Language::class, cascade: ['persist'])]
    private ?Language $language = null;

    private ?string $languageCode = null;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getId(): string
    {
        return (string) $this->id;
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

    public function getLanguageCode(): ?string
    {
        return $this->language?->getCode() ?? $this->languageCode;
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
            'language' => $this->getLanguageCode() ?? $this->language->getCode(),
        ];
    }
}
