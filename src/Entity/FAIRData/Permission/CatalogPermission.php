<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Catalog;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permission_catalog")
 */
class CatalogPermission extends Permission
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="catalogs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected User $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Catalog", cascade={"persist"}, fetch = "EAGER", inversedBy="permissions")
     * @ORM\JoinColumn(name="catalog_id", referencedColumnName="id")
     */
    private Catalog $catalog;

    public function __construct(User $user, PermissionType $type, Catalog $catalog)
    {
        parent::__construct($user, $type);
        $this->catalog = $catalog;
    }

    public function getEntity(): Catalog
    {
        return $this->catalog;
    }
}
