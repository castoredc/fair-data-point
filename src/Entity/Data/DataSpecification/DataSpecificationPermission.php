<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Entity\Enum\PermissionType;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission_data_specification")
 */
class DataSpecificationPermission
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="dataSpecifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="DataSpecification", cascade={"persist"}, fetch = "EAGER", inversedBy="permissions")
     * @ORM\JoinColumn(name="data_specification_id", referencedColumnName="id")
     */
    private DataSpecification $dataSpecification;

    /** @ORM\Column(type="PermissionType") */
    private PermissionType $type;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDataSpecification(): DataSpecification
    {
        return $this->dataSpecification;
    }

    public function getType(): PermissionType
    {
        return $this->type;
    }

    public function setType(PermissionType $type): void
    {
        $this->type = $type;
    }
}
