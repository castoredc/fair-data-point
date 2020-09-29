<?php
declare(strict_types=1);

namespace App\Traits;

use App\Security\User;

trait CreatedBy
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Security\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     *
     * @Gedmo\Mapping\Annotation\Blameable(on="create")
     */
    private ?User $createdBy = null;

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
}
