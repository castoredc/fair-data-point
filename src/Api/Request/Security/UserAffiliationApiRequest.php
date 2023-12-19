<?php
declare(strict_types=1);

namespace App\Api\Request\Security;

use App\Api\Request\GroupedApiRequest;
use App\Entity\Enum\DepartmentSource;
use App\Entity\Enum\OrganizationSource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

class UserAffiliationApiRequest extends GroupedApiRequest implements GroupSequenceProviderInterface
{
    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(groups = {"OrganizationDb"})
     */
    private ?string $organizationId = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private ?string $organizationSource;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(groups = {"OrganizationManual"})
     */
    private ?string $organizationName = null;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(groups = {"OrganizationManual"})
     */
    private ?string $organizationCity = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Country()
     */
    private string $organizationCountry;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(groups = {"DepartmentDb"})
     */
    private ?string $departmentId = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private ?string $departmentSource;

    /**
     * @Assert\Type("string")
     * @Assert\NotBlank(groups = {"DepartmentManual"})
     */
    private ?string $departmentName = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $position;

    protected function parse(): void
    {
        $this->organizationSource = $this->getFromNestedData('organization', 'source');
        $this->departmentSource = $this->getFromNestedData('department', 'source');

        $this->organizationId = $this->getFromNestedData('organization', 'id');
        $this->organizationName = $this->getFromNestedData('organization', 'name');
        $this->organizationCity = $this->getFromNestedData('organization', 'city');
        $this->organizationCountry = $this->getFromData('country');

        $this->departmentId = $this->getFromNestedData('department', 'id');
        $this->departmentName = $this->getFromNestedData('department', 'name');

        $this->position = $this->getFromData('position');
    }

    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    public function getOrganizationSource(): OrganizationSource
    {
        return OrganizationSource::fromString($this->organizationSource);
    }

    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    public function getOrganizationCity(): ?string
    {
        return $this->organizationCity;
    }

    public function getOrganizationCountry(): string
    {
        return $this->organizationCountry;
    }

    public function getDepartmentId(): ?string
    {
        return $this->departmentId;
    }

    public function getDepartmentSource(): DepartmentSource
    {
        return DepartmentSource::fromString($this->departmentSource);
    }

    public function getDepartmentName(): ?string
    {
        return $this->departmentName;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    /** @inheritDoc */
    public function getGroupSequence(): array|Assert\GroupSequence
    {
        $sequence = ['UserAffiliationApiRequest'];

        if ($this->getOrganizationSource()->isManual()) {
            $sequence[] = 'OrganizationManual';
        } else {
            $sequence[] = 'OrganizationDb';
        }

        if ($this->getDepartmentSource()->isManual()) {
            $sequence[] = 'DepartmentManual';
        } else {
            $sequence[] = 'DepartmentDb';
        }

        return $sequence;
    }
}
