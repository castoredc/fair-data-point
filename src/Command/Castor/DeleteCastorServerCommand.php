<?php
declare(strict_types=1);

namespace App\Command\Castor;

final class DeleteCastorServerCommand
{
    /** @param int $id The server ID that shall be deleted */
    public function __construct(private int $id)
    {
    }

    /** @return int The server ID that shall be deleted */
    public function getId(): int
    {
        return $this->id;
    }
}
