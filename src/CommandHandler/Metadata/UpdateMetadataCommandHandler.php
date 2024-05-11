<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\UpdateMetadataCommand;
use App\Entity\DataSpecification\Common\OptionGroupOption;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\Metadata\MetadataValue;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Exception\InvalidAgentType;
use App\Exception\InvalidMetadataValue;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\OntologyNotFound;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;
use function json_encode;

#[AsMessageHandler]
class UpdateMetadataCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    /**
     * @throws NotFound
     * @throws NoAccessPermission
     */
    public function __invoke(UpdateMetadataCommand $command): void
    {
        $metadata = $command->getMetadata();
        $metadataModelVersion = $metadata->getMetadataModelVersion();

        if (! $this->security->isGranted('edit', $metadata->getEntity())) {
            throw new NoAccessPermission();
        }

        $fields = [];

        foreach ($metadataModelVersion->getFields() as $field) {
            $fields[$field->getId()] = $field;
        }

        foreach ($metadata->getValues() as $metadataValue) {
            $this->em->remove($metadataValue);
        }

        foreach ($command->getValues() as $fieldId => $value) {
            $field = $fields[$fieldId];
            $node = $field->getNode();
            $value = $this->parseValue($field, $value);

            $metadataValue = new MetadataValue($metadata, $node, $value);
            $this->em->persist($metadataValue);

            $metadata->addValue($metadataValue);
        }

        $this->em->persist($metadata);

        $this->em->flush();
    }

    /** @throws InvalidMetadataValue
     * @throws OntologyNotFound
     * @throws InvalidAgentType
     */
    private function parseValue(MetadataModelField $field, mixed $value): string
    {
        $node = $field->getNode();

        if ($value === '' || $value === [] || $value === null) {
            return '';
        }

        if ($node->isAnnotatedValue()) {
            // Validate option
            if ($field->getFieldType()->hasOptionGroup()) {
                if ($field->getFieldType()->isCheckboxes()) {
                    $value = $this->getOptionGroupOptions($field, $value);
                } else {
                    $value = $this->getOptionGroupOption($field, $value);
                }
            }

            if ($field->getFieldType()->isCountryPicker()) {
                $value = $this->getCountry($field, $value)->getCode();
            }

            if ($field->getFieldType()->isLicensePicker()) {
                $value = $this->getLicense($field, $value)->getSlug();
            }

            if ($field->getFieldType()->isLanguagePicker()) {
                $value = $this->getLanguage($field, $value)->getCode();
            }

            if ($field->getFieldType()->isOntologyConceptBrowser()) {
                $value = $this->parseOntologyConcepts($field, $value);
            }

            if ($field->getFieldType()->isAgentSelector()) {
                $value = $this->parseAgents($field, $value);
            }
        } else {
            if ($node->getDataType()->isLangString()) {
                $value = $this->parseLocalizedText($field, $value)->toArray();
            }
        }

        $value = json_encode($value);

        return $value !== false ? $value : '';
    }

    private function parseLocalizedText(MetadataModelField $field, ?array $values): ?LocalizedText
    {
        if ($values === null) {
            return null;
        }

        $texts = new ArrayCollection();

        foreach ($values as $item) {
            $text = new LocalizedTextItem($item['text']);
            $text->setLanguageCode($item['language']);
            $text->setLanguage($this->getLanguage($field, $item['language']));

            $texts->add($text);
        }

        return new LocalizedText($texts);
    }

    /**
     * @param array<mixed> $agents
     *
     * @return Agent[]
     *
     * @throws InvalidAgentType
     * @throws InvalidMetadataValue
     */
    private function parseAgents(MetadataModelField $field, array $agents): array
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

            if ($agent->hasId()) {
                $repository = $this->em->getRepository(Agent::class);

                $dbAgent = $repository->find($agent->getId());

                if ($agent instanceof Department && $dbAgent instanceof Department) {
                    $organization = $agent->getOrganization();
                    $dbOrganization = $dbAgent->getOrganization();

                    $dbAgent->setName($agent->getName());
                    $dbAgent->setAdditionalInformation($agent->getAdditionalInformation());

                    $dbOrganization->setName($organization->getName());
                    $dbOrganization->setCountryCode($organization->getCountryCode());
                    $dbOrganization->setHomepage($organization->getHomepage());
                    $dbOrganization->setCity($organization->getCity());
                    $dbOrganization->setCoordinatesLatitude($organization->getCoordinatesLatitude());
                    $dbOrganization->setCoordinatesLongitude($organization->getCoordinatesLongitude());

                    $dbAgent->setOrganization($dbOrganization);
                } elseif ($agent instanceof Organization && $dbAgent instanceof Organization) {
                    $dbAgent->setName($agent->getName());
                    $dbAgent->setCountryCode($agent->getCountryCode());
                    $dbAgent->setHomepage($agent->getHomepage());
                    $dbAgent->setCity($agent->getCity());
                    $dbAgent->setCoordinatesLatitude($agent->getCoordinatesLatitude());
                    $dbAgent->setCoordinatesLongitude($agent->getCoordinatesLongitude());
                } elseif ($agent instanceof Person && $dbAgent instanceof Person) {
                    if ($this->security->isGranted('ROLE_ADMIN')) {
                        $dbAgent->setFirstName($agent->getFirstName());
                        $dbAgent->setMiddleName($agent->getMiddleName());
                        $dbAgent->setLastName($agent->getLastName());
                        $dbAgent->setEmail($agent->getEmail());
                        $dbAgent->setOrcid($agent->getOrcid());
                        $dbAgent->setPhoneNumber($agent->getPhoneNumber());
                        $dbAgent->setName($dbAgent->getFullName());
                    }
                } else {
                    throw new InvalidAgentType();
                }

                $newAgent = $dbAgent;
            } else {
                $newAgent = $agent;
            }

            if (
                $agent instanceof Department && $newAgent instanceof Department && $agent->getOrganization()
                    ->getCountryCode() !== null
            ) {
                $newAgent->getOrganization()->setCountry(
                    $this->getCountry($field, $agent->getOrganization()->getCountryCode())
                );
            }

            if (
                $agent instanceof Organization && $newAgent instanceof Organization && $agent->getCountryCode(
                ) !== null
            ) {
                $newAgent->setCountry($this->getCountry($field, $agent->getCountryCode()));
            }

            $return[] = $newAgent;
        }

        return $return;
    }

    /** @throws InvalidMetadataValue */
    private function getCountry(MetadataModelField $field, string $countryCode): Country
    {
        $repository = $this->em->getRepository(Country::class);

        $country = $repository->find($countryCode);

        if ($country === null) {
            throw new InvalidMetadataValue($field->getTitle());
        }

        assert($country instanceof Country);

        return $country;
    }

    /** @throws InvalidMetadataValue */
    private function getLanguage(MetadataModelField $field, string $languageCode): Language
    {
        $repository = $this->em->getRepository(Language::class);

        $language = $repository->find($languageCode);

        if ($language === null) {
            throw new InvalidMetadataValue($field->getTitle());
        }

        return $language;
    }

    /** @throws InvalidMetadataValue */
    private function getLicense(MetadataModelField $field, string $licenseId): License
    {
        $repository = $this->em->getRepository(License::class);

        $license = $repository->find($licenseId);

        if ($license === null) {
            throw new InvalidMetadataValue($field->getTitle());
        }

        return $license;
    }

    /**
     * @param OntologyConcept[] $concepts
     *
     * @return OntologyConcept[]
     */
    private function parseOntologyConcepts(MetadataModelField $field, array $concepts): array
    {
        $return = [];

        $ontologyRepository = $this->em->getRepository(Ontology::class);
        $ontologyConceptRepository = $this->em->getRepository(OntologyConcept::class);

        foreach ($concepts as $concept) {
            $ontology = $ontologyRepository->find($concept->getOntology()->getId());

            if ($ontology === null) {
                throw new OntologyNotFound();
            }

            $concept->setOntology($ontology);

            $dbConcept = $ontologyConceptRepository->findByOntologyAndCode($ontology, $concept->getCode());

            if ($dbConcept !== null) {
                $return[] = $dbConcept;
            } else {
                $return[] = $concept;
            }
        }

        return $return;
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
}
