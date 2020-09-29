<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\CSV\CSVDistribution;

class AddCSVDistributionContentCommand
{
    private CSVDistribution $distribution;

    private string $type;

    private string $value;

    public function __construct(CSVDistribution $distribution, string $type, string $value)
    {
        $this->distribution = $distribution;
        $this->type = $type;
        $this->value = $value;
    }

    public function getDistribution(): CSVDistribution
    {
        return $this->distribution;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
