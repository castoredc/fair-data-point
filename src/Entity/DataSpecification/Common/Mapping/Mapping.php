<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Mapping;

use App\Entity\DataSpecification\Common\Version;
use App\Entity\Study;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_specification_mappings')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['element' => 'ElementMapping', 'group' => 'GroupMapping'])]
abstract class Mapping
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'study', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Study::class, inversedBy: 'mappings', cascade: ['persist'])]
    private Study $study;

    #[ORM\JoinColumn(name: 'version', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\DataSpecification\Common\Version::class)]
    private Version $version;

    public function __construct(Study $study, Version $version)
    {
        $this->study = $study;
        $this->version = $version;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function setStudy(Study $study): void
    {
        $this->study = $study;
    }
}
