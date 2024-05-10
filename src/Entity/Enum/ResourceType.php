<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\Study;

/**
 * @method static static catalog()
 * @method static static dataset()
 * @method static static distribution()
 * @method static static study()
 * @method bool isCatalog()
 * @method bool isDataset()
 * @method bool isDistribution()
 * @method bool isStudy()
 * @inheritDoc
 */
class ResourceType extends Enum
{
    private const CATALOG = 'catalog';
    private const DATASET = 'dataset';
    private const DISTRIBUTION = 'distribution';
    private const STUDY = 'study';

    private const CLASS_MAPPING = [
        self::CATALOG => Catalog::class,
        self::DATASET => Dataset::class,
        self::DISTRIBUTION => Distribution::class,
        self::STUDY => Study::class,
    ];

    public const TYPES = [
        self::CATALOG,
        self::DATASET,
        self::DISTRIBUTION,
        self::STUDY,
    ];

    /** @return class-string<object> */
    public function getClass(): string
    {
        return self::CLASS_MAPPING[$this->toString()];
    }
}
