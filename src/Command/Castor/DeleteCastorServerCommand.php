<?php
declare(strict_types=1);

namespace App\Command\Castor;

class DeleteCastorServerCommand
{
    private int $id;

    /** @param int $id The server ID that shall be deleted */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /** @return int The server ID that shall be deleted */
    public function getId(): int
    {
        return $this->id;
    }
}
