<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\Common\Group;

/**
 * @ORM\Entity
 * @ORM\Table(name="data_dictionary_group")
 * @ORM\HasLifecycleCallbacks
 */
class DataDictionaryGroup extends Group
{
}
