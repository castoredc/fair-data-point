<?php
declare(strict_types=1);

namespace App\Entity\Data\DataDictionary;

use App\Entity\Data\DataSpecification\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_group")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryGroup extends Group
{
}
