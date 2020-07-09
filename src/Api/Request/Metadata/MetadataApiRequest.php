<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\FAIRData\Organization;
use App\Entity\FAIRData\Person;
use App\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @var mixed[]|null
     * @AppAssert\AgentArray
     */
    private $publishers;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->description = $this->getFromData('description');
        $this->language = $this->getFromData('language');
        $this->license = $this->getFromData('license');
        $this->versionUpdate = $this->getFromData('versionUpdate');
        $this->publishers = $this->getFromData('publishers');
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

    /** @return Agent[] */
    public function getPublishers(): array
    {
        return $this->generateAgents($this->publishers);
    }

    /** @param mixed[] $items */
    private function generateLocalizedText(array $items): LocalizedText
    {
        $texts = new ArrayCollection();

        foreach ($items as $item) {
            $text = new LocalizedTextItem($item['text']);
            $text->setLanguageCode($item['language']);

            $texts->add($text);
        }

        return new LocalizedText($texts);
    }

    /**
     * @param mixed[] $items
     *
     * @return Agent[]
     */
    private function generateAgents(array $items): array
    {
        $agents = [];

        foreach ($items as $item) {
            $id = isset($item['id']) && $item['id'] !== null ? $item['id'] : null;

            if ($item['type'] === Organization::TYPE) {
                $agents[] = Organization::fromData($item, $id);
            } elseif ($item['type'] === Person::TYPE) {
                $agents[] = Person::fromData($item, $id);
            }
        }

        return $agents;
    }
}
