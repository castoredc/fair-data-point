<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\DataModelModule;

class UpdateDataModelModuleCommand
{
    /** @var DataModelModule */
    private $module;

    /** @var string */
    private $title;

    /** @var int */
    private $order;

    public function __construct(DataModelModule $module, string $title, int $order)
    {
        $this->module = $module;
        $this->title = $title;
        $this->order = $order;
    }

    public function getModule(): DataModelModule
    {
        return $this->module;
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
