<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Enum\StudySource;

class CreateStudyCommand
{
    private StudySource $source;

    private ?string $sourceId = null;

    private ?string $sourceServer = null;

    private ?string $name = null;

    private bool $manuallyEntered;

    public function __construct(
        StudySource $source,
        ?string $sourceId,
        ?string $sourceServer,
        ?string $name,
        bool $manuallyEntered
    ) {
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->sourceServer = $sourceServer;
        $this->name = $name;
        $this->manuallyEntered = $manuallyEntered;
    }

    public function getSource(): StudySource
    {
        return $this->source;
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

    public function isManuallyEntered(): bool
    {
        return $this->manuallyEntered;
    }
}
