<?php
declare(strict_types=1);

namespace App\Command\Dataset;

use App\Entity\FAIRData\Dataset;

class UpdateDatasetCommand
{
    public function __construct(private Dataset $dataset, private string $slug, private bool $published)
    {
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPublished(): bool
    {
        return $this->published;
    }
}
