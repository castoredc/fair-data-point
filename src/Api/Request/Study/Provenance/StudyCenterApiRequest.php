<?php
declare(strict_types=1);

namespace App\Api\Request\Study\Provenance;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\OrganizationSource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

class StudyCenterApiRequest extends SingleApiRequest implements GroupSequenceProviderInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $source;

    /**
     * @Assert\NotBlank(groups = {"database"})
     * @Assert\Type("string")
     */
    private ?string $id;

    /** @Assert\Country() */
    private string $country;

    /**
     * @Assert\NotBlank(groups = {"manual"})
     * @Assert\Type("string")
     */
    private ?string $name;

    /**
     * @Assert\NotBlank(groups = {"manual"})
     * @Assert\Type("string")
     */
    private ?string $city;

    protected function parse(): void
    {
        $this->source = $this->getFromData('source');
        $this->country = $this->getFromData('country');

        $this->id = $this->getFromData('id');

        $this->name = $this->getFromData('name');
        $this->city = $this->getFromData('city');
    }

    public function getSource(): OrganizationSource
    {
        return OrganizationSource::fromString($this->source);
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getGroupSequence(): array|Assert\GroupSequence
    {
        $sequence = ['StudyCenterApiRequest'];

        if ($this->getSource()->isDatabase()) {
            $sequence[] = 'database';
        } elseif ($this->getSource()->isManual()) {
            $sequence[] = 'manual';
        }

        return $sequence;
    }
}
