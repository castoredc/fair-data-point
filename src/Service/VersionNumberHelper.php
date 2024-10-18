<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Enum\VersionType;
use App\Entity\Version;

class VersionNumberHelper
{
    public const DEFAULT_VERSION_NUMBER = '0.0.1';

    public function getDefaultVersion(): Version
    {
        return new Version(self::DEFAULT_VERSION_NUMBER);
    }

    public function getNewVersion(?Version $currentVersion, VersionType $versionUpdate): Version
    {
        if ($currentVersion === null) {
            return new Version(self::DEFAULT_VERSION_NUMBER);
        }

        $version = new Version();

        if ($versionUpdate->isMajor()) {
            $version->setMajor($currentVersion->getMajor() + 1);
            $version->setMinor(0);
            $version->setPatch(0);
        } elseif ($versionUpdate->isMinor()) {
            $version->setMajor($currentVersion->getMajor());
            $version->setMinor($currentVersion->getMinor() + 1);
            $version->setPatch(0);
        } elseif ($versionUpdate->isPatch()) {
            $version->setMajor($currentVersion->getMajor());
            $version->setMinor($currentVersion->getMinor());
            $version->setPatch($currentVersion->getPatch() + 1);
        }

        return $version;
    }
}
