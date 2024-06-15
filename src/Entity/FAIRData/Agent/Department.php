<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Agent;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use function uniqid;

/** @ORM\Entity */
class Department extends Agent
{
    public const TYPE = 'department';

    /**
     * @ORM\ManyToOne(targetEntity="Organization",cascade={"persist"}, inversedBy="departments")
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     */
    private ?Organization $organization = null;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $additionalInformation = null;

    public function __construct(?string $slug, string $name, Organization $organization, ?string $additionalInformation)
    {
        $slugify = new Slugify();

        if ($slug === null) {
            $slug = $slugify->slugify($organization->getName() . ' ' . $name . ' ' . uniqid());
        }

        parent::__construct($slug, $name);

        $this->organization = $organization;
        $this->additionalInformation = $additionalInformation;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }

    public function setOrganization(?Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function setAdditionalInformation(?string $additionalInformation): void
    {
        $this->additionalInformation = $additionalInformation;
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data, Organization $organization): self
    {
        $department = new self(
            $data['slug'] ?? null,
            $data['name'],
            $organization,
            $data['additionalInformation'] ?? null,
        );

        if ($data['id'] !== null) {
            $department->setId($data['id']);
        }

        return $department;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = $this->organization->toArray();
        $data['type'] = self::TYPE;

        $data[self::TYPE] = [
            'id' => $this->id,
            'name' => $this->getName(),
            'additionalInformation' => $this->additionalInformation,
        ];

        return $data;
    }
}
