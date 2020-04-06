<?php
declare(strict_types=1);

namespace App\Type;

use App\Entity\Iri;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class IriType extends Type
{
    public const IRI = 'iri';

    /** @inheritDoc */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /** @inheritDoc */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Iri
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new Iri($value);
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
        return self::IRI;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
