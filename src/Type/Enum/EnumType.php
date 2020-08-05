<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\Enum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class EnumType extends Type
{
    /** @var string */
    protected $name = '';

    /** @var string */
    protected $class = Enum::class;

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

        return $this->class::fromString($value);
    }

    /** @inheritDoc */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return $value->toString();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
