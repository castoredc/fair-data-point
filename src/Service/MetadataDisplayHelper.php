<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;
use App\Entity\Metadata\Metadata;
use function json_decode;

class MetadataDisplayHelper
{
    public static function getValueForDisplay(
        Metadata $metadata,
        MetadataModelDisplaySetting $displaySetting,
    ): mixed {
        $value = $metadata->getValueForNode($displaySetting->getNode());
        $value = $value !== null ? json_decode($value->getValue(), true) : null;

        return $value ?? null;
    }
}
