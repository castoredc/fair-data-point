<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Model;

use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Predicate;

interface Triple
{
    public function getSubject(): Node;

    public function setSubject(Node $subject): void;

    public function getPredicate(): Predicate;

    public function setPredicate(Predicate $predicate): void;

    public function getObject(): Node;

    public function setObject(Node $object): void;
}
