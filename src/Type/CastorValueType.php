<?php
declare(strict_types=1);

namespace App\Type;

use App\Entity\Enum\CastorValueType as Enum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class CastorValueType extends Type
{
    /** @inheritDoc */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /** @inheritDoc */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Enum
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Enum::fromString($value);
    }

    /** @inheritDoc */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return $value->toString();
    }

    public function getName(): string
    {
        return 'CastorValueType';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
