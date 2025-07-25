<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\InvalidVersion;
use function count;
use function preg_match;

class Version
{
    public const VERSION_REGEX = '/^(\d+)\.(\d+)\.(\d+)$/m';

    private int $major;

    private int $minor;

    private int $patch;

    public function __construct(?string $version = null)
    {
        if ($version === null) {
            return;
        }

        preg_match(self::VERSION_REGEX, $version, $matches);

        if (count($matches) !== 4) {
            throw new InvalidVersion();
        }

        $this->major = (int) $matches[1];
        $this->minor = (int) $matches[2];
        $this->patch = (int) $matches[3];
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->major . '.' . $this->minor . '.' . $this->patch;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function getPatch(): int
    {
        return $this->patch;
    }

    public function setMajor(int $major): void
    {
        $this->major = $major;
    }

    public function setMinor(int $minor): void
    {
        $this->minor = $minor;
    }

    public function setPatch(int $patch): void
    {
        $this->patch = $patch;
    }
}
