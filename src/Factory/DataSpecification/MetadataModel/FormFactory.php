<?php
declare(strict_types=1);

namespace App\Factory\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\Common\OptionGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\ResourceType;
use Doctrine\Common\Collections\ArrayCollection;

class FormFactory
{
    /**
     * @param array<mixed>                 $data
     * @param ArrayCollection<Node>        $nodes
     * @param ArrayCollection<OptionGroup> $optionGroups
     */
    public function createFromJson(MetadataModelVersion $version, array $data, ArrayCollection $nodes, ArrayCollection $optionGroups): MetadataModelForm
    {
        $form = new MetadataModelForm(
            $data['title'],
            $data['order'],
            ResourceType::fromString($data['resourceType']),
            $version
        );

        foreach ($data['fields'] as $field) {
            $field = new MetadataModelField(
                $field['title'],
                $field['description'] ?? null,
                $field['order'],
                $nodes->get($field['node']),
                MetadataFieldType::fromString($field['fieldType']),
                $field['optionGroup'] !== null ? $optionGroups->get($field['optionGroup']) : null,
                ResourceType::fromString($field['resourceType']),
                $field['isRequired'],
                $form
            );

            $form->addField($field);
        }

        return $form;
    }
}
