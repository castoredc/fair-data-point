<?php
declare(strict_types=1);

namespace App\Traits;

use App\Security\User;

trait UpdatedBy
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Security\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     *
     * @Gedmo\Mapping\Annotation\Blameable(on="update")
     */
    private ?User $updatedBy = null;

    public function getUpdatedBy(): User
    {
        return $this->updatedBy;
    }
}
