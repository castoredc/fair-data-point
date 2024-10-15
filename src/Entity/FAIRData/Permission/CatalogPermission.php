<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Permission;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Catalog;
use App\Security\Permission;
use App\Security\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'permission_catalog')]
#[ORM\Entity]
class CatalogPermission extends Permission
{
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'catalogs')]
    protected User $user;

    #[ORM\JoinColumn(name: 'catalog_id', referencedColumnName: 'id')]
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Catalog::class, cascade: ['persist'], fetch: 'EAGER', inversedBy: 'permissions')]
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
