<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary;

use App\Entity\Data\DataSpecification\DataSpecification;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionary extends DataSpecification
{
}
