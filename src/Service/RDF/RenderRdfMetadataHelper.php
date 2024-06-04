<?php
declare(strict_types=1);

namespace App\Service\RDF;

use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Exception\NotFound;
use App\Service\UriHelper;
use EasyRdf\Graph;
use EasyRdf\Literal;
use Symfony\Bundle\SecurityBundle\Security;
use function json_decode;

class RenderRdfMetadataHelper extends RdfRenderHelper
{
    public function __construct(
        private UriHelper $uriHelper,
        private Security $security,
    ) {
    }

    /** @throws NotFound */
    public function renderEntity(MetadataEnrichedEntity $entity, Graph $graph): Graph
    {
        $metadata = $entity->getLatestMetadata();
        $metadataModel = $metadata->getMetadataModelVersion();
        $modules = $metadataModel->getGroupsForResourceType($metadata->getResourceType());

        foreach ($modules as $module) {
            $this->renderModule($entity, $graph, $module);
        }

        return $graph;
    }

    private function renderModule(MetadataEnrichedEntity $entity, Graph $graph, MetadataModelGroup $module): void
    {
        $triples = $module->getTriples();

        foreach ($triples as $triple) {
            $subject = $graph->resource($this->getURI($entity, $triple->getSubject()));
            $predicate = $triple->getPredicate()->getIri()->getValue();
            $object = $triple->getObject();
            $isLiteral = ($object instanceof LiteralNode && ! $object->getDataType()->isUrl() || ($object instanceof ValueNode && ! $object->isAnnotatedValue()));

            $values = $this->getValue($entity, $object, $isLiteral);

            foreach ($values as $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                if ($isLiteral) {
                    $graph->addLiteral($subject, $predicate, $value);
                } else {
                    $graph->add($subject, $predicate, $graph->resource($value));
                }
            }
        }
    }

    private function getURI(MetadataEnrichedEntity $entity, Node $node): string
    {
        $uri = '';

        if ($node instanceof RecordNode || $node instanceof ChildrenNode || $node instanceof ParentsNode) {
            $uri = $this->uriHelper->getUri($entity);
        } elseif ($node instanceof InternalIriNode) {
            $uri = $this->uriHelper->getUri($entity) . '/' . $node->getSlug();

//            if ($node->isRepeated()) {
//                $uri .= '/' . $data->getInstance()->getId();
//            }
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        }

        return $uri;
    }

    private function getValue(MetadataEnrichedEntity $entity, Node $node, bool $isLiteral): ?array
    {
        $metadata = $entity->getLatestMetadata();
        $return = [];

        if ($node instanceof LiteralNode) {
            $value = $node->getValue();

            if ($node->isPlaceholder()) {
                $placeholderType = $node->getPlaceholderType();

                if ($placeholderType->isResourceUrl()) {
                    $value = $this->uriHelper->getUri($entity);
                }

                if ($placeholderType->isUpdatedAt()) {
                    $value = $metadata->getUpdatedAt()?->format('Y-m-d\TH:i:s');
                }

                if ($placeholderType->isCreatedAt()) {
                    $value = $metadata->getCreatedAt()?->format('Y-m-d\TH:i:s');
                }

                if ($placeholderType->isMetadataVersion()) {
                    $value = $metadata->getVersion()->getValue();
                }

                if ($entity instanceof Distribution) {
                    if ($placeholderType->isDistributionAccessUrl()) {
                        $value = $entity->getAccessUrl();
                    }

                    if ($placeholderType->isDistributionMediaType()) {
                        $value = $entity->getMediaType();
                    }
                }
            }

            if ($value === null || $value === '') {
                return [];
            }

            return $isLiteral ? [new Literal($value, null, 'xsd:' . $node->getDataType()->toString())] : [$value];
        }

        if ($node instanceof ChildrenNode) {
            return $this->getURIs($entity->getChildren($node->getResourceType()), $node);
        }

        if ($node instanceof ParentsNode) {
            return $this->getURIs($entity->getParents($node->getResourceType()), $node);
        }

        if (! ($node instanceof ValueNode)) {
            return [$this->getURI($entity, $node)];
        }

        // 'Plain' value
        $nodeValue = $metadata->getValueForNode($node);

        if ($nodeValue === null) {
            return [];
        }

        $value = json_decode($nodeValue->getValue(), true);

        if ($value === null || $value === '') {
            return [];
        }

        $field = $node->getField();

        if ($node->isAnnotatedValue()) {
            // Validate option
//            if ($field->getFieldType()->hasOptionGroup()) {
//                if ($field->getFieldType()->isCheckboxes()) {
//                    $value = $this->getOptionGroupOptions($field, $value);
//                } else {
//                    $value = $this->getOptionGroupOption($field, $value);
//                }
//            }

//            if ($field->getFieldType()->isCountryPicker()) {
//                $value = $this->getCountry($field, $value)->getCode();
//            }
//
//            if ($field->getFieldType()->isLicensePicker()) {
//                $value = $this->getLicense($field, $value)->getSlug();
//            }
//
//            if ($field->getFieldType()->isLanguagePicker()) {
//                $value = $this->getLanguage($field, $value)->getCode();
//            }
//
//            if ($field->getFieldType()->isOntologyConceptBrowser()) {
//                $value = $this->parseOntologyConcepts($field, $value);
//            }
//
//            if ($field->getFieldType()->isAgentSelector()) {
//                $value = $this->parseAgents($field, $value);
//            }
        } elseif ($node->getDataType()->isLangString()) {
            $value = LocalizedText::fromArray($value);

            foreach ($value->getTexts() as $text) {
                $return[] = new Literal($text->getText(), $text->getLanguageCode());
            }
        }

        return $return;
    }

    /** @param MetadataEnrichedEntity[] $entities */
    private function getURIs(array $entities, Node $node)
    {
        $urls = [];

        foreach ($entities as $entity) {
            if ($this->security->isGranted('view', $entity)) {
                $urls[] = $this->getURI($entity, $node);
            }
        }

        return $urls;
    }
}
