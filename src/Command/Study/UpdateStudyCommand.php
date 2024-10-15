<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Study;

final class UpdateStudyCommand
{
    public function __construct(
        private Study $study,
        private string $slug,
        private bool $published,
        private ?string $sourceId = null,
        private ?int $sourceServer = null,
        private ?string $name = null,
    ) {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function getSourceServer(): ?int
    {
        return $this->sourceServer;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }
}
