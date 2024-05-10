<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\ResourceType as Enum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use function array_map;
use function explode;
use function implode;

class ResourcesType extends Type
{
    /** @inheritDoc */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /** @inheritDoc */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return [];
        }

        $values = explode(',', $value);

        return array_map(static fn ($value) => Enum::fromString($value), $values);
    }

    /** @inheritDoc */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return implode(',', array_map(static fn (Enum $enum) => $enum->toString(), $value));
    }

    public function getName(): string
    {
        return 'ResourcesType';
    }
}
