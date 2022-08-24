<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Data\Log\SparqlQueryLog;
use Doctrine\ORM\EntityRepository;

/**
 * @method SparqlQueryLog[] findAll()
 * @method ?SparqlQueryLog find(int $id)
 */
class SparqlQueryLogRepository extends EntityRepository
{
}
