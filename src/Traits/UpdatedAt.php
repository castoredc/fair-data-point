<?php
declare(strict_types=1);

namespace App\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait UpdatedAt
{
    /** @ORM\Column(type="datetime", nullable=true) */
    private ?DateTime $updatedAt = null;

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /** @ORM\PreUpdate */
    public function onUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }
}
