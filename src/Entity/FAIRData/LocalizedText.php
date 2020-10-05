<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="text_localized")
 */
class LocalizedText
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\OneToMany(targetEntity="LocalizedTextItem", mappedBy="parent", cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="texts", referencedColumnName="id")
     *
     * @var Collection<mixed, LocalizedTextItem>
     */
    private Collection $texts;

    /**
     * @param Collection<mixed, LocalizedTextItem> $texts
     */
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
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Collection<LocalizedTextItem>
     */
    public function getTexts(): Collection
    {
        return $this->texts;
    }

    /**
     * @param Collection<LocalizedTextItem> $texts
     */
    public function setTexts(Collection $texts): void
    {
        $this->texts = $texts;
    }

    /**
     * @return array<mixed>
     */
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
}
