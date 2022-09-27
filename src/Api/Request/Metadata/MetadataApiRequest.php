<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

abstract class MetadataApiRequest extends SingleApiRequest
{
    /**
     * @var mixed[]|null
     * @AppAssert\LocalizedText
     */
    private ?array $title = null;

    /**
     * @var mixed[]|null
     * @AppAssert\LocalizedText
     */
    private ?array $description = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private ?string $language = null;

    /** @Assert\Type("string") */
    private ?string $license = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $versionUpdate;

    /**
     * @var mixed[]|null
     * @AppAssert\AgentArray
     */
    private ?array $publishers = null;

    protected function parse(): void
    {
        $this->title = $this->getFromData('title');
        $this->description = $this->getFromData('description');
        $this->language = $this->getFromData('language');
        $this->license = $this->getFromData('license');
        $this->versionUpdate = $this->getFromData('versionUpdate');
        $this->publishers = $this->getFromData('publishers');
    }

    public function getTitle(): ?LocalizedText
    {
        return $this->generateLocalizedText($this->title);
    }

    public function getDescription(): ?LocalizedText
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
    protected function generateLocalizedText(?array $items): ?LocalizedText
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

    /**
     * @param mixed[] $items
     *
     * @return Agent[]
     */
    private function generateAgents(array $items): array
    {
        $agents = [];

        foreach ($items as $item) {
            $agent = null;

            if ($item['type'] === Organization::TYPE) {
                $organization = Organization::fromData($item['organization']);
                $agent = $organization;
            } elseif ($item['type'] === Department::TYPE) {
                $organization = Organization::fromData($item['organization']);
                $department = Department::fromData($item['department'], $organization);
                $agent = $department;
            } elseif ($item['type'] === Person::TYPE) {
                $agent = Person::fromData($item['person']);
            }

            $agents[] = $agent;
        }

        return $agents;
    }
}
