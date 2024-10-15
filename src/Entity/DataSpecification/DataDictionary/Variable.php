<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\Enum\DataDictionaryDataType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_dictionary_variable')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Variable extends Element
{
    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $format = null;

    #[ORM\Column(type: 'DataDictionaryDataType')]
    private DataDictionaryDataType $dataType;

    public function __construct(Version $version, string $title, ?string $description, string $name, Group $group, DataDictionaryDataType $dataType, int $order)
    {
        parent::__construct($version, $title, $description);

        $this->name = $name;
        $this->group = $group;
        $this->dataType = $dataType;
        $this->order = $order;
    }

    public function getDataType(): DataDictionaryDataType
    {
        return $this->dataType;
    }

    public function setDataType(DataDictionaryDataType $dataType): void
    {
        $this->dataType = $dataType;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
