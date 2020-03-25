<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Department extends Agent
{
    /**
     * @ORM\ManyToOne(targetEntity="Organization",cascade={"persist"}, inversedBy="departments")
     * @ORM\JoinColumn(name="organization", referencedColumnName="id")
     *
     * @var Organization
     */
    private $organization;

    /**
     * @ORM\Column(type="text")
     *
     * @var string|null
     */
    private $additionalInformation;

    public function __construct(?string $slug, string $name, Organization $organization, ?string $additionalInformation)
    {
        $slugify = new Slugify();

        if($slug === null)
        {
            $slug = $slugify->slugify($organization->getName() . ' ' . $name . ' ' . time());
        }
        parent::__construct($slug, $name);

        $this->organization = $organization;
        $this->additionalInformation = $additionalInformation;
    }

    /**
     * @return Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @return string|null
     */
    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }
}
