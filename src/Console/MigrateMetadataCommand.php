<?php
/** @phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint */
declare(strict_types=1);

namespace App\Console;

use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\Metadata\MetadataValue;
use App\Entity\Metadata\StudyMetadata\ParticipatingCenter;
use App\Entity\Metadata\StudyMetadata\StudyTeamMember;
use App\Entity\Study;
use App\Entity\Terminology\OntologyConcept;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function array_merge;
use function assert;
use function json_encode;

class MigrateMetadataCommand extends Command
{
    /** @phpcs:ignore */
    protected static $defaultName = 'app:metadata:migrate';

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Insert new Castor metadata schema');
    }

    /** @inheritDoc */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(
            [
                'Migrate metadata',
                '============',
                '',
            ]
        );

        $metadataModel = $this->em->getRepository(MetadataModel::class)->find(CreateMetadataModelCommand::METADATA_MODEL_ID);
        assert($metadataModel instanceof MetadataModel);

        $metadataModelVersion = $metadataModel->getLatestVersion();

        assert($metadataModelVersion instanceof MetadataModelVersion);

        $nodes = $metadataModelVersion->getValueNodes();

        // FDP
        /** @var FAIRDataPoint[] $fdps */
        $fdps = $this->em->getRepository(FAIRDataPoint::class)->findAll();

        foreach ($fdps as $fdp) {
            $metadata = $fdp->getLatestMetadata();

            if ($metadata === null) {
                continue;
            }

            $fdp->setDefaultMetadataModel($metadataModel);
            $metadata->setMetadataModelVersion($metadataModelVersion);

            foreach ($metadata->getValues() as $metadataValue) {
                $this->em->remove($metadataValue);
            }

            $title = new MetadataValue($metadata, $nodes['FAIR Data Point title'], (string) json_encode($metadata->getLegacyTitle()?->toArray()));
            $description = new MetadataValue($metadata, $nodes['FAIR Data Point description'], (string) json_encode($metadata->getLegacyDescription()?->toArray()));
            $language = new MetadataValue($metadata, $nodes['FAIR Data Point language'], (string) json_encode($metadata->getLanguage()?->getCode()));
            $license = new MetadataValue($metadata, $nodes['FAIR Data Point license'], (string) json_encode($metadata->getLicense()?->getSlug()));

            $agents = $metadata->getPublishers()->map(static function (Agent $agent) {
                return $agent->toArray();
            })->toArray();

            $publishers = new MetadataValue($metadata, $nodes['FAIR Data Point publishers'], (string) json_encode($agents));

            $metadata->addValue($title);
            $metadata->addValue($description);
            $metadata->addValue($language);
            $metadata->addValue($license);
            $metadata->addValue($publishers);

            $this->em->persist($title);
            $this->em->persist($description);
            $this->em->persist($language);
            $this->em->persist($license);
            $this->em->persist($publishers);
            $this->em->persist($metadata);
        }

        // Catalog

        /** @var Catalog[] $catalogs */
        $catalogs = $this->em->getRepository(Catalog::class)->findAll();

        foreach ($catalogs as $catalog) {
            $metadata = $catalog->getLatestMetadata();

            if ($metadata === null) {
                continue;
            }

            $catalog->setDefaultMetadataModel($metadataModel);
            $metadata->setMetadataModelVersion($metadataModelVersion);

            foreach ($metadata->getValues() as $metadataValue) {
                $this->em->remove($metadataValue);
            }

            $title = new MetadataValue($metadata, $nodes['Catalog title'], (string) json_encode($metadata->getLegacyTitle()?->toArray()));
            $description = new MetadataValue($metadata, $nodes['Catalog description'], (string) json_encode($metadata->getLegacyDescription()?->toArray()));
            $language = new MetadataValue($metadata, $nodes['Catalog language'], (string) json_encode($metadata->getLanguage()?->getCode()));
            $license = new MetadataValue($metadata, $nodes['Catalog license'], (string) json_encode($metadata->getLicense()?->getSlug()));
            $homepage = new MetadataValue($metadata, $nodes['Catalog homepage'], (string) json_encode($metadata->getHomepage()?->getValue()));
            $logo = new MetadataValue($metadata, $nodes['Catalog logo'], (string) json_encode($metadata->getLogo()?->getValue()));

            $agents = $metadata->getContacts()->map(static function (Agent $agent) {
                return $agent->toArray();
            })->toArray();

            $contactPoint = new MetadataValue($metadata, $nodes['Catalog contact point'], (string) json_encode($agents));

            $agents = $metadata->getPublishers()->map(static function (Agent $agent) {
                return $agent->toArray();
            })->toArray();

            $publishers = new MetadataValue($metadata, $nodes['Catalog publishers'], (string) json_encode($agents));

            $themes = $metadata->getThemeTaxonomies()->map(static function (OntologyConcept $concept) {
                return $concept->toArray();
            })->toArray();

            $themes = new MetadataValue($metadata, $nodes['Catalog theme taxonomy'], (string) json_encode($themes));

            $metadata->addValue($title);
            $metadata->addValue($description);
            $metadata->addValue($language);
            $metadata->addValue($license);
            $metadata->addValue($homepage);
            $metadata->addValue($logo);
            $metadata->addValue($contactPoint);
            $metadata->addValue($publishers);
            $metadata->addValue($themes);

            $this->em->persist($title);
            $this->em->persist($description);
            $this->em->persist($language);
            $this->em->persist($license);
            $this->em->persist($homepage);
            $this->em->persist($logo);
            $this->em->persist($contactPoint);
            $this->em->persist($publishers);
            $this->em->persist($themes);
            $this->em->persist($metadata);
        }

        // Dataset

        /** @var Dataset[] $datasets */
        $datasets = $this->em->getRepository(Dataset::class)->findAll();

        foreach ($datasets as $dataset) {
            $metadata = $dataset->getLatestMetadata();

            if ($metadata === null) {
                continue;
            }

            $dataset->setDefaultMetadataModel($metadataModel);
            $metadata->setMetadataModelVersion($metadataModelVersion);

            foreach ($metadata->getValues() as $metadataValue) {
                $this->em->remove($metadataValue);
            }

            $title = new MetadataValue($metadata, $nodes['Dataset title'], (string) json_encode($metadata->getLegacyTitle()?->toArray()));
            $description = new MetadataValue($metadata, $nodes['Dataset description'], (string) json_encode($metadata->getLegacyDescription()?->toArray()));
            $language = new MetadataValue($metadata, $nodes['Dataset language'], (string) json_encode($metadata->getLanguage()?->getCode()));
            $license = new MetadataValue($metadata, $nodes['Dataset license'], (string) json_encode($metadata->getLicense()?->getSlug()));
            $keywords = new MetadataValue($metadata, $nodes['Dataset keywords'], (string) json_encode($metadata->getKeyword()?->toArray()));

            $agents = $metadata->getContacts()->map(static function (Agent $agent) {
                return $agent->toArray();
            })->toArray();

            $contactPoint = new MetadataValue($metadata, $nodes['Dataset contact point'], (string) json_encode($agents));

            $agents = $metadata->getPublishers()->map(static function (Agent $agent) {
                return $agent->toArray();
            })->toArray();

            $publishers = new MetadataValue($metadata, $nodes['Catalog publishers'], (string) json_encode($agents));

            $themes = $metadata->getThemes()->map(static function (OntologyConcept $concept) {
                return $concept->toArray();
            })->toArray();

            $themes = new MetadataValue($metadata, $nodes['Dataset themes'], (string) json_encode($themes));

            $metadata->addValue($title);
            $metadata->addValue($description);
            $metadata->addValue($language);
            $metadata->addValue($license);
            $metadata->addValue($keywords);
            $metadata->addValue($contactPoint);
            $metadata->addValue($publishers);
            $metadata->addValue($themes);

            $this->em->persist($title);
            $this->em->persist($description);
            $this->em->persist($language);
            $this->em->persist($license);
            $this->em->persist($keywords);
            $this->em->persist($contactPoint);
            $this->em->persist($publishers);
            $this->em->persist($themes);
            $this->em->persist($metadata);
        }

        // Distribution

        /** @var Distribution[] $distributions */
        $distributions = $this->em->getRepository(Distribution::class)->findAll();

        foreach ($distributions as $distribution) {
            $metadata = $distribution->getLatestMetadata();

            if ($metadata === null) {
                continue;
            }

            $distribution->setDefaultMetadataModel($metadataModel);
            $metadata->setMetadataModelVersion($metadataModelVersion);

            foreach ($metadata->getValues() as $metadataValue) {
                $this->em->remove($metadataValue);
            }

            $title = new MetadataValue($metadata, $nodes['Distribution title'], (string) json_encode($metadata->getLegacyTitle()?->toArray()));
            $description = new MetadataValue($metadata, $nodes['Distribution description'], (string) json_encode($metadata->getLegacyDescription()?->toArray()));
            $language = new MetadataValue($metadata, $nodes['Distribution language'], (string) json_encode($metadata->getLanguage()?->getCode()));
            $license = new MetadataValue($metadata, $nodes['Distribution license'], (string) json_encode($metadata->getLicense()?->getSlug()));

            $agents = $metadata->getPublishers()->map(static function (Agent $agent) {
                return $agent->toArray();
            })->toArray();

            $publishers = new MetadataValue($metadata, $nodes['Distribution publishers'], (string) json_encode($agents));

            $metadata->addValue($title);
            $metadata->addValue($description);
            $metadata->addValue($language);
            $metadata->addValue($license);
            $metadata->addValue($publishers);

            $this->em->persist($title);
            $this->em->persist($description);
            $this->em->persist($language);
            $this->em->persist($license);
            $this->em->persist($publishers);
            $this->em->persist($metadata);
        }

        // Study
        $repository = $this->em->getRepository(Language::class);
        $english = $repository->find('en');

        /** @var Study[] $studies */
        $studies = $this->em->getRepository(Study::class)->findAll();

        foreach ($studies as $study) {
            $metadata = $study->getLatestMetadata();

            if ($metadata === null) {
                continue;
            }

            $study->setDefaultMetadataModel($metadataModel);
            $metadata->setMetadataModelVersion($metadataModelVersion);

            foreach ($metadata->getValues() as $metadataValue) {
                $this->em->remove($metadataValue);
            }

            $briefSummary = new MetadataValue($metadata, $nodes['Study brief summary'], (string) json_encode($this->generateLocalizedText($metadata->getBriefSummary(), $english)));
            $briefTitle = new MetadataValue($metadata, $nodes['Study brief title'], (string) json_encode($this->generateLocalizedText($metadata->getBriefName(), $english)));

            $agents = array_merge(...$metadata->getCenters()->map(static function (ParticipatingCenter $center) {
                if ($center->getDepartments()->count() > 0) {
                    return $center->getDepartments()->map(static function (Department $department) {
                        return $department->toArray();
                    })->toArray();
                }

                return [$center->getOrganization()->toArray()];
            })->toArray());

            $centers = new MetadataValue($metadata, $nodes['Study centers'], (string) json_encode($agents));

            $conditions = $metadata->getConditions()->map(static function (OntologyConcept $concept) {
                return $concept->toArray();
            })->toArray();

            $conditions = new MetadataValue($metadata, $nodes['Study condition'], (string) json_encode($conditions));

            $estimatedStartDate = new MetadataValue($metadata, $nodes['Study estimated start date'], (string) json_encode($metadata->getEstimatedStudyStartDate()?->format('Y-m-d')));
            $estimatedCompletionDate = new MetadataValue($metadata, $nodes['Study estimated completion date'], (string) json_encode($metadata->getEstimatedStudyCompletionDate()?->format('Y-m-d')));
            $estimatedParticipants = new MetadataValue($metadata, $nodes['Study estimated total number of participants'], (string) json_encode($metadata->getEstimatedEnrollment()));

            if ($metadata->getIntervention() !== null) {
                $intervention = $this->generateLocalizedText($metadata->getIntervention()->getText(), $english);
            } else {
                $intervention = null;
            }

            $intervention = new MetadataValue($metadata, $nodes['Study intervention'], (string) json_encode($intervention));

            $keywords = new MetadataValue($metadata, $nodes['Study keywords'], (string) json_encode($metadata->getKeywords()?->toArray()));
            $method = new MetadataValue($metadata, $nodes['Study method'], (string) json_encode($metadata->getMethodType()?->toString()));

            $scientificTitle = new MetadataValue($metadata, $nodes['Study official (scientific) title'], (string) json_encode($this->generateLocalizedText($metadata->getScientificName(), $english)));

            $status = new MetadataValue($metadata, $nodes['Study status'], (string) json_encode($metadata->getRecruitmentStatus()?->toString()));

            $agents = $metadata->getStudyTeam()->map(static function (StudyTeamMember $agent) {
                return $agent->getPerson()->toArray();
            })->toArray();

            $team = new MetadataValue($metadata, $nodes['Study team'], (string) json_encode($agents));
            $type = new MetadataValue($metadata, $nodes['Study type'], (string) json_encode($metadata->getType()?->toString()));

            $metadata->addValue($briefSummary);
            $metadata->addValue($briefTitle);
            $metadata->addValue($centers);
            $metadata->addValue($conditions);
            $metadata->addValue($estimatedStartDate);
            $metadata->addValue($estimatedCompletionDate);
            $metadata->addValue($estimatedParticipants);
            $metadata->addValue($intervention);
            $metadata->addValue($keywords);
            $metadata->addValue($method);
            $metadata->addValue($scientificTitle);
            $metadata->addValue($status);
            $metadata->addValue($team);
            $metadata->addValue($type);

            $this->em->persist($briefSummary);
            $this->em->persist($briefTitle);
            $this->em->persist($centers);
            $this->em->persist($conditions);
            $this->em->persist($estimatedStartDate);
            $this->em->persist($estimatedCompletionDate);
            $this->em->persist($estimatedParticipants);
            $this->em->persist($intervention);
            $this->em->persist($keywords);
            $this->em->persist($method);
            $this->em->persist($scientificTitle);
            $this->em->persist($status);
            $this->em->persist($team);
            $this->em->persist($type);

            $this->em->persist($metadata);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

    private function generateLocalizedText(?string $text, Language $language): mixed
    {
        if ($text === null) {
            return null;
        }

        $text = new LocalizedTextItem($text);
        $text->setLanguageCode($language->getCode());
        $text->setLanguage($language);

        return (new LocalizedText(new ArrayCollection([$text])))->toArray();
    }
}
