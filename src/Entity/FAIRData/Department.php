<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use function uniqid;

/**
 * @ORM\Entity
 */
class Department extends Agent
{
    /**
     * @ORM\ManyToOne(targetEntity="Organization",cascade={"persist"}, inversedBy="departments")
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     *
     * @var Organization|null
     */
    private $organization;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $additionalInformation;

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
}
