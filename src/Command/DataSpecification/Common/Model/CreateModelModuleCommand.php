<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

abstract class CreateModelModuleCommand
{
    private string $title;

    private int $order;

    public function __construct(string $title, int $order)
    {
        $this->title = $title;
        $this->order = $order;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }
}
