<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Form\Field;
use App\Entity\Enum\StudySource;
use App\Entity\Study;
use App\Security\CastorServer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'study_castor')]
#[ORM\Entity]
class CastorStudy extends Study
{
    /** @var ArrayCollection<string, Field>|null */
    private ?ArrayCollection $fields = null;

    #[ORM\JoinColumn(name: 'server', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: CastorServer::class)]
    private ?CastorServer $server = null;

    /** @return ArrayCollection<string, Field>|null */
    public function getFields(): ?ArrayCollection
    {
        return $this->fields;
    }

    public function __construct(?string $sourceId, ?string $name, ?string $slug)
    {
        parent::__construct(StudySource::castor(), $sourceId, $name, $slug);
    }

    /** @param ArrayCollection<string, Field>|null $fields */
    public function setFields(?ArrayCollection $fields): void
    {
        $this->fields = $fields;
    }

    public function getServer(): ?CastorServer
    {
        return $this->server;
    }

    public function setServer(?CastorServer $server): void
    {
        $this->server = $server;
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data): self
    {
        return new self(
            $data['study_id'] ?? null,
            $data['name'] ?? null,
            $data['slug'] ?? null
        );
    }
}
