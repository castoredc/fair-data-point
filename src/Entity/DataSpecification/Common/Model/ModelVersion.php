<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Model;

use App\Entity\DataSpecification\Common\OptionGroup;
use App\Entity\Enum\NodeType;
use Doctrine\Common\Collections\Collection;

interface ModelVersion
{
    /** @return Node[] */
    public function getNodesByType(NodeType $nodeType): array;

    /** @return Collection<Predicate> */
    public function getPredicates(): Collection;

    public function addPredicate(Predicate $predicate): void;

    /** @return Collection<NamespacePrefix> */
    public function getPrefixes(): Collection;

    public function addPrefix(NamespacePrefix $prefix): void;

    public function removePrefix(NamespacePrefix $prefix): void;

    public function addNode(Node $node): void;

    /** @return Collection<OptionGroup> */
    public function getOptionGroups(): Collection;

    public function addOptionGroup(OptionGroup $optionGroup): void;

    public function removeOptionGroup(OptionGroup $optionGroup): void;
}
