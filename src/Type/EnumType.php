<?php
declare(strict_types=1);

namespace App\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use function implode;
use function in_array;
use function sprintf;

abstract class EnumType extends Type
{
    /**
     * @return string[]
     */
    abstract protected function getValues(): array;

    /** @inheritDoc */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return sprintf('ENUM(\'%s\')', implode('\', \'', $this->getValues()));
    }

    /** @inheritDoc */
    public function convertToPHPValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }

    /** @inheritDoc */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (! in_array($value, $this->getValues(), true)) {
            throw new InvalidArgumentException("Invalid '" . static::class . "' value given.");
        }

        return (string) $value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getName(): string
    {
        return static::class;
    }
}