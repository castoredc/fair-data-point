<?php
declare(strict_types=1);

namespace App\Service\RDF;

use App\Entity\Enum\DependencyOperatorType;
use App\Entity\Enum\XsdDataType;
use DateTimeImmutable;
use function boolval;
use function floatval;

class RdfRenderHelper
{
    protected function transformValue(?XsdDataType $dataType, string $value): string
    {
        if ($dataType === null) {
            return $value;
        }

        if ($dataType->isDateTimeType()) {
            $date = new DateTimeImmutable($value);

            if ($dataType->isDateTime()) {
                return $date->format('Y-m-d\TH:i:s');
            }

            if ($dataType->isDate()) {
                return $date->format('Y-m-d');
            }

            if ($dataType->isTime()) {
                return $date->format('H:i:s');
            }

            if ($dataType->isGDay()) {
                return '---' . $date->format('d');
            }

            if ($dataType->isGMonth()) {
                return '--' . $date->format('m');
            }

            if ($dataType->isGYear()) {
                return $date->format('Y');
            }

            if ($dataType->isGYearMonth()) {
                return $date->format('Y-m');
            }

            if ($dataType->isGMonthDay()) {
                return '--' . $date->format('m-d');
            }
        } elseif ($dataType->isNumberType()) {
            return $value;
        } elseif ($dataType->isBooleanType()) {
            return (string) boolval($value);
        }

        return $value;
    }

    protected function compareValue(DependencyOperatorType $operator, ?XsdDataType $dataType, ?string $value, string $compareTo): bool
    {
        if ($dataType === null) {
            $dataType = XsdDataType::string();
        }

        if ($value === null) {
            return $operator->isNull();
        }

        if ($dataType->isDateTimeType()) {
            $value = new DateTimeImmutable($value);
            $compareTo = new DateTimeImmutable($compareTo);
        } elseif ($dataType->isNumberType()) {
            $value = floatval($value);
            $compareTo = floatval($compareTo);
        } elseif ($dataType->isBooleanType()) {
            $value = boolval($value);
            $compareTo = boolval($compareTo);
        }

        if ($operator->isNull()) {
            return false;
        }

        if ($operator->isNotNull()) {
            return true;
        }

        if ($operator->isEqual()) {
            return $value === $compareTo;
        }

        if ($operator->isNotEqual()) {
            return $value !== $compareTo;
        }

        if ($operator->isSmallerThan()) {
            return $value < $compareTo;
        }

        if ($operator->isSmallerThanOrEqualTo()) {
            return $value <= $compareTo;
        }

        if ($operator->isGreaterThan()) {
            return $value > $compareTo;
        }

        if ($operator->isGreaterThanOrEqualTo()) {
            return $value >= $compareTo;
        }

        return false;
    }
}
