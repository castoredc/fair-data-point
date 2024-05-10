<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\Enum\StudySource;

final class CreateStudyCommand
{
    public function __construct(
        private StudySource $source,
        private ?string $sourceId = null,
        private ?int $sourceServer = null,
        private ?string $name = null,
        private bool $manuallyEntered,
    ) {
    }

    public function getSource(): StudySource
    {
        return $this->source;
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

    public function isManuallyEntered(): bool
    {
        return $this->manuallyEntered;
    }
}
