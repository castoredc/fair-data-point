<?php
declare(strict_types=1);

namespace App\Message\Dataset;

use App\Entity\FAIRData\Dataset;

class UpdateDatasetCommand
{
    private Dataset $dataset;

    private string $slug;

    private bool $published;

    public function __construct(Dataset $dataset, string $slug, bool $published)
    {
        $this->dataset = $dataset;
        $this->slug = $slug;
        $this->published = $published;
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
