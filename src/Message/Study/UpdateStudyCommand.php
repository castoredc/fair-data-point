<?php
declare(strict_types=1);

namespace App\Message\Study;

use App\Entity\Study;

class UpdateStudyCommand
{
    private Study $study;

    private ?string $sourceId = null;

    private ?string $sourceServer = null;

    private ?string $name = null;

    private string $slug;

    private bool $published;

    public function __construct(
        Study $study,
        ?string $sourceId,
        ?string $sourceServer,
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

    public function getSourceServer(): ?string
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
