<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\RDF\RDFDistribution;

class AddRDFDistributionModuleCommand
{
    /** @var RDFDistribution */
    private $distribution;

    /** @var string */
    private $title;

    /** @var int */
    private $order;

    public function __construct(string $title, int $order, RDFDistribution $distribution)
    {
        $this->title = $title;
        $this->order = $order;
        $this->distribution = $distribution;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }
}
