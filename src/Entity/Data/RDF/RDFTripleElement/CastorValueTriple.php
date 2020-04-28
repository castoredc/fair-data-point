<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF\RDFTripleElement;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Record;
use App\Entity\Data\RDF\RDFTripleObject;
use App\Entity\Enum\CastorValueType;
use App\Exception\InvalidValueType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="rdf_triple_element_castor_value")
 */
class CastorValueTriple extends RDFTripleElement implements RDFTripleObject
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\Form\Field",cascade={"persist"})
     * @ORM\JoinColumn(name="field", referencedColumnName="id")
     *
     * @var Field
     */
    private $field;

    /**
     * @ORM\Column(type="CastorValueType", name="value_type")
     *
     * @var CastorValueType
     */
    private $type;

    public function __construct(Field $field, CastorValueType $type)
    {
        $this->field = $field;
        $this->type = $type;
    }

    public function getLabel(): string
    {
        return $this->field->getFieldLabel();
    }

    /**
     * @throws InvalidValueType
     */
    public function getValue(Record $record): string
    {
        // TODO: Add other types than study

        $fieldResult = $record->getData()->getStudy()->getFieldResultByFieldId($this->field->getId());

        if ($this->type === CastorValueType::plain()) {
            return $fieldResult->getValue();
        }

        if ($this->field->getOptionGroup() === null) {
            throw new InvalidValueType();
        }

        if ($this->type === CastorValueType::entity()) {
            $option = $this->field->getOptionGroup()->getOptionByValue($fieldResult->getValue());

            return $record->getId() . '/' . $this->field->getSlug() . '/' . $option->getSlug();
        }

        if ($this->type === CastorValueType::annotated()) {
            // TODO: Add annotations
            $option = $this->field->getOptionGroup()->getOptionByValue($fieldResult->getValue());

            return '';
        }

        return '';
    }
}
