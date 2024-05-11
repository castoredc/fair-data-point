<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="text_localized")
 */
class LocalizedText
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string $id;

    /**
     * @ORM\OneToMany(targetEntity="LocalizedTextItem", mappedBy="parent", cascade={"persist"}, fetch = "EAGER")
     *
     * @var Collection<LocalizedTextItem>
     */
    private Collection $texts;

    /** @param Collection<LocalizedTextItem> $texts */
    public function __construct(Collection $texts)
    {
        $this->texts = $texts;

        foreach ($this->texts as $text) {
            /** @var LocalizedTextItem $text */
            $text->setParent($this);
        }
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /** @return Collection<LocalizedTextItem> */
    public function getTexts(): Collection
    {
        return $this->texts;
    }

    public function hasTexts(): bool
    {
        return ! $this->texts->isEmpty();
    }

    /** @param Collection<LocalizedTextItem> $texts */
    public function setTexts(Collection $texts): void
    {
        $this->texts = $texts;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->texts as $text) {
            /** @var LocalizedTextItem $text */
            $array[] = $text->toArray();
        }

        return $array;
    }

    public function getTextByLanguageString(string $language): ?LocalizedTextItem
    {
        foreach ($this->texts as $text) {
            if ($text->getLanguage()->getCode() === $language) {
                return $text;
            }
        }

        return null;
    }

    public static function fromArray(?array $items): ?LocalizedText
    {
        if ($items === null) {
            return null;
        }

        $texts = new ArrayCollection();

        foreach ($items as $item) {
            $text = new LocalizedTextItem($item['text']);
            $text->setLanguageCode($item['language']);

            $texts->add($text);
        }

        return new LocalizedText($texts);
    }
}
