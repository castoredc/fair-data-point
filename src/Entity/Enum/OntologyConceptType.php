<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static concept()
 * @method static static individual()
 * @method bool isConcept()
 * @method bool isIndividual()
 * @inheritDoc
 */
class OntologyConceptType extends Enum
{
    private const CONCEPT = 'concept';
    private const INDIVIDUAL = 'individual';
}
