<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static rdf()
 * @method static static csv()
 * @method bool isRdf()
 * @method bool isCsv()
 * @inheritDoc
 */
class DistributionType extends Enum
{
    private const RDF = 'rdf';
    private const CSV = 'csv';
}
