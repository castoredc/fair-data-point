<?php

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;

abstract class MetadataApiRequest extends SingleApiRequest
{
    /**
     * @var mixed[]|null
     * @AppAssert\LocalizedText
     */
    private $title;

    /**
     * @var mixed[]|null
     * @AppAssert\LocalizedText
     */
    private $description;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $language;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $license;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $versionUpdate;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->description = $this->getFromData('description');
        $this->language = $this->getFromData('language');
        $this->license = $this->getFromData('license');
        $this->versionUpdate = $this->getFromData('versionUpdate');
    }

    public function getTitle(): LocalizedText
    {
        return $this->generateLocalizedText($this->title);
    }

    public function getDescription(): LocalizedText
    {
        return $this->generateLocalizedText($this->description);
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function getVersionUpdate(): VersionType
    {
        return VersionType::fromString($this->versionUpdate);
    }

    /** @param mixed[] $items */
    private function generateLocalizedText(array $items): LocalizedText
    {
        $texts = new ArrayCollection();

        foreach($items as $item) {
            $text = new LocalizedTextItem($item['text']);
            $text->setLanguageCode($item['language']);

            $texts->add($text);
        }

        return new LocalizedText($texts);
    }
}