<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Exception\InvalidAgentType;
use App\Exception\OntologyNotFound;
use App\Service\VersionNumberHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

abstract class CreateMetadataCommandHandler implements MessageHandlerInterface
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

    protected EntityManagerInterface $em;

    protected Security $security;

    protected VersionNumberHelper $versionNumberHelper;

    public function __construct(EntityManagerInterface $em, Security $security, VersionNumberHelper $versionNumberHelper)
    {
        $this->em = $em;
        $this->security = $security;
        $this->versionNumberHelper = $versionNumberHelper;
    }

    protected function parseLocalizedText(?LocalizedText $localizedText): ?LocalizedText
    {
        if ($localizedText === null) {
            return null;
        }

        foreach ($localizedText->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $text->setLanguage($this->getLanguage($text->getLanguageCode()));
        }

        return $localizedText;
    }

    /**
     * @param Agent[] $publishers
     */
    protected function parsePublishers(array $publishers): ArrayCollection
    {
        $return = new ArrayCollection();

        foreach ($publishers as $agent) {
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

            if ($agent instanceof Department && $newAgent instanceof Department && $agent->getOrganization()->getCountryCode() !== null) {
                $newAgent->getOrganization()->setCountry($this->getCountry($agent->getOrganization()->getCountryCode()));
            }

            if ($agent instanceof Organization && $newAgent instanceof Organization && $agent->getCountryCode() !== null) {
                $newAgent->setCountry($this->getCountry($agent->getCountryCode()));
            }

            $return->add($newAgent);
        }

        return $return;
    }

    protected function getCountry(string $countryCode): Country
    {
        $repository = $this->em->getRepository(Country::class);

        $country = $repository->find($countryCode);
        assert($country instanceof Country);

        return $country;
    }

    protected function getLanguage(string $languageCode): Language
    {
        $repository = $this->em->getRepository(Language::class);

        $language = $repository->find($languageCode);
        assert($language instanceof Language);

        return $language;
    }

    protected function getLicense(string $licenseId): License
    {
        $repository = $this->em->getRepository(License::class);

        $license = $repository->find($licenseId);
        assert($license instanceof License);

        return $license;
    }

    /**
     * @param OntologyConcept[] $concepts
     *
     * @return OntologyConcept[]
     */
    protected function parseOntologyConcepts(array $concepts): array
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
}
