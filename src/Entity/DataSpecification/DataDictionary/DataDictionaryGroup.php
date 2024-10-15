<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataDictionary;

use App\Entity\DataSpecification\Common\Group;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_dictionary_group')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class DataDictionaryGroup extends Group
{
}
