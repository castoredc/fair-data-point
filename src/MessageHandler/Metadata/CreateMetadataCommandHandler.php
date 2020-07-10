<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\FAIRData\Organization;
use App\Entity\FAIRData\Person;
use App\Entity\Version;
use App\Exception\InvalidAgentType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

abstract class CreateMetadataCommandHandler implements MessageHandlerInterface
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

    /** @var EntityManagerInterface */
    protected $em;

    /** @var Security */
    protected $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    protected function updateVersionNumber(?Version $currentVersion, VersionType $versionUpdate): Version
    {
        if ($currentVersion === null) {
            return new Version(self::DEFAULT_VERSION_NUMBER);
        }

        $version = new Version();

        if ($versionUpdate->isMajor()) {
            $version->setMajor($currentVersion->getMajor() + 1);
            $version->setMinor(0);
            $version->setPatch(0);
        } elseif ($versionUpdate->isMinor()) {
            $version->setMajor($currentVersion->getMajor());
            $version->setMinor($currentVersion->getMinor() + 1);
            $version->setPatch(0);
        } elseif ($versionUpdate->isPatch()) {
            $version->setMajor($currentVersion->getMajor());
            $version->setMinor($currentVersion->getMinor());
            $version->setPatch($currentVersion->getPatch() + 1);
        }

        return $version;
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

                /** @var Agent $newAgent */
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
                        $dbAgent->generateFullName();
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

        /** @var Country $country */
        $country = $repository->find($countryCode);

        return $country;
    }

    protected function getLanguage(string $languageCode): Language
    {
        $repository = $this->em->getRepository(Language::class);

        /** @var Language $language */
        $language = $repository->find($languageCode);

        return $language;
    }

    protected function getLicense(string $licenseId): License
    {
        $repository = $this->em->getRepository(License::class);

        /** @var License $license */
        $license = $repository->find($licenseId);

        return $license;
    }
}
