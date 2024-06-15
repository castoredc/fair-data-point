<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

abstract class CreateModelModuleCommand
{
    public function __construct(private string $title, private int $order)
    {
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
