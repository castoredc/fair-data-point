<?php
declare(strict_types=1);

namespace App\Type;

use App\Entity\Version;
use App\Exception\InvalidVersion;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class VersionType extends Type
{
    /** @inheritDoc */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @throws InvalidVersion
     *
     * @inheritDoc
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Version
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new Version($value);
    }

    /** @inheritDoc */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return (string) $value;
    }

    public function getName(): string
    {
        return 'version';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
