<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;

/**
 * @method static static catalog()
 * @method static static dataset()
 * @method static static distribution()
 * @method static static model()
 * @method bool isCatalog()
 * @method bool isDataset()
 * @method bool isDistribution()
 * @method bool isModel()
 * @inheritDoc
 */
class PermissionsEnabledEntityType extends Enum
{
    private const CATALOG = 'catalog';
    private const DATASET = 'dataset';
    private const DISTRIBUTION = 'distribution';
    private const MODEL = 'model';

    private const CLASS_MAPPING = [
        self::CATALOG => Catalog::class,
        self::DATASET => Dataset::class,
        self::DISTRIBUTION => Distribution::class,
        self::MODEL => DataModel::class,
    ];

    /** @return class-string<object> */
    public function getClass(): string
    {
        return self::CLASS_MAPPING[$this->toString()];
    }
}
