<?php
declare(strict_types=1);

namespace App\Service\RDF;

use App\Entity\DataSpecification\Common\OptionGroupOption;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Iri;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Exception\InvalidMetadataValue;
use App\Exception\NotFound;
use App\Exception\OntologyNotFound;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use EasyRdf\Graph;
use EasyRdf\Literal;
use Symfony\Bundle\SecurityBundle\Security;
use function json_decode;

class RenderRdfMetadataHelper extends RdfRenderHelper
{
    public function __construct(
        private EntityManagerInterface $em,
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
        } elseif ($node instanceof ExternalIriNode) {
            $uri = $node->getIri()->getValue();
        }

        return $uri;
    }

    /** @return array<mixed> */
    private function getValue(MetadataEnrichedEntity $entity, Node $node, bool $isLiteral): array
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
                        $value = $this->uriHelper->getBaseUri() . $entity->getAccessUrl();
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
            if ($field->getFieldType()->hasOptionGroup()) {
                if ($field->getFieldType()->isCheckboxes()) {
                    $return[] = $this->getOptionGroupOptions($field, $value);
                } else {
                    $value[] = $this->getOptionGroupOption($field, $value);
                }
            }

            if ($field->getFieldType()->isCountryPicker()) {
                $country = $this->em->getRepository(Country::class)->find($value);

                if ($country === null) {
                    return [];
                }

                $return[] = $this->uriHelper->getUri($country);
            }

            if ($field->getFieldType()->isLicensePicker()) {
                $license = $this->em->getRepository(License::class)->find($value);

                if ($license === null) {
                    return [];
                }

                $return[] = $this->uriHelper->getUri($license);
            }

            if ($field->getFieldType()->isLanguagePicker()) {
                $language = $this->em->getRepository(Language::class)->find($value);

                if ($language === null) {
                    return [];
                }

                $return[] = $this->uriHelper->getUri($language);
            }

            if ($field->getFieldType()->isOntologyConceptBrowser()) {
                $return = $this->parseOntologyConcepts($value);
            }

            if ($field->getFieldType()->isAgentSelector()) {
                $return = $this->parseAgents($value);
            }
        } elseif ($node->getDataType()->isLangString()) {
            $value = LocalizedText::fromArray($value);

            foreach ($value->getTexts() as $text) {
                $return[] = new Literal($text->getText(), $text->getLanguageCode());
            }
        }

        return $return;
    }

    /**
     * @param MetadataEnrichedEntity[] $entities
     *
     * @return string[]
     */
    private function getURIs(array $entities, Node $node): array
    {
        $urls = [];

        foreach ($entities as $entity) {
            if ($this->security->isGranted('view', $entity)) {
                $urls[] = $this->getURI($entity, $node);
            }
        }

        return $urls;
    }

    private function getOptionGroupOption(MetadataModelField $field, string $value): OptionGroupOption
    {
        $optionGroup = $field->getOptionGroup();

        $option = $optionGroup->getOption($value);

        if ($option === null) {
            throw new InvalidMetadataValue($field->getTitle());
        }

        return $option;
    }

    /**
     * @param array<string> $values
     *
     * @return OptionGroupOption[]
     *
     * @throws InvalidMetadataValue
     */
    private function getOptionGroupOptions(MetadataModelField $field, array $values): array
    {
        $optionGroup = $field->getOptionGroup();
        $options = [];

        foreach ($values as $value) {
            $option = $optionGroup->getOption($value);

            if ($option === null) {
                throw new InvalidMetadataValue($field->getTitle());
            }

            $options[] = $option;
        }

        return $options;
    }

    /**
     * @param mixed[] $concepts
     *
     * @return string[]
     */
    private function parseOntologyConcepts(array $concepts): array
    {
        $return = [];

        $ontologyRepository = $this->em->getRepository(Ontology::class);
        $ontologyConceptRepository = $this->em->getRepository(OntologyConcept::class);

        foreach ($concepts as $conceptData) {
            $ontology = $ontologyRepository->find($conceptData['ontology']['id']);

            if ($ontology === null) {
                throw new OntologyNotFound();
            }

            $dbConcept = $ontologyConceptRepository->findByOntologyAndCode($ontology, $conceptData['code']);

            if ($dbConcept !== null) {
                $return[] = $dbConcept->getUrl()->getValue();
            } else {
                $return[] = (new OntologyConcept(
                    new Iri($conceptData['url']),
                    $conceptData['code'],
                    $ontology,
                    $conceptData['displayName'],
                ))->getUrl()->getValue();
            }
        }

        return $return;
    }

    /**
     * @param mixed[] $agents
     *
     * @return string[]
     */
    private function parseAgents(array $agents): array
    {
        $return = [];

        foreach ($agents as $item) {
            $agent = null;

            if ($item['type'] === Organization::TYPE) {
                $organization = Organization::fromData($item['organization']);
                $agent = $organization;
            } elseif ($item['type'] === Department::TYPE) {
                $organization = Organization::fromData($item['organization']);
                $department = Department::fromData($item['department'], $organization);
                $agent = $department;
            } elseif ($item['type'] === Person::TYPE) {
                $agent = Person::fromData($item['person']);
            }

            if (! $agent->hasId()) {
                continue;
            }

            $repository = $this->em->getRepository(Agent::class);

            $dbAgent = $repository->find($agent->getId());

            $return[] = $this->uriHelper->getUri($dbAgent);
        }

        return $return;
    }
}
