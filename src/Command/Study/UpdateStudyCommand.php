<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Study;

final class UpdateStudyCommand
{
    private Study $study;

    private ?string $sourceId = null;

    private ?int $sourceServer = null;

    private ?string $name = null;

    private string $slug;

    private bool $published;

    public function __construct(
        Study $study,
        ?string $sourceId,
        ?int $sourceServer,
        ?string $name,
        string $slug,
        bool $published
    ) {
        $this->study = $study;
        $this->sourceId = $sourceId;
        $this->sourceServer = $sourceServer;
        $this->name = $name;
        $this->slug = $slug;
        $this->published = $published;
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
