<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary;

use App\Entity\Enum\NodeType;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="data_dictionary_variable")
 * @ORM\HasLifecycleCallbacks
 */
class Variable
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="DataDictionaryGroup", inversedBy="variables", cascade={"persist"})
     * @ORM\JoinColumn(name="group", referencedColumnName="id", nullable=false)
     */
    private DataDictionaryGroup $group;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $description = null;

    public function __construct(DataDictionaryGroup $group, string $title, ?string $description)
    {
        $this->group = $group;
        $this->title = $title;
        $this->description = $description;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGroup(): DataDictionaryGroup
    {
        return $this->group;
    }

    public function setGroup(DataDictionaryGroup $group): void
    {
        $this->group = $group;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getType(): ?NodeType
    {
        return null;
    }

    public function getValue(): ?string
    {
        return null;
    }
}
