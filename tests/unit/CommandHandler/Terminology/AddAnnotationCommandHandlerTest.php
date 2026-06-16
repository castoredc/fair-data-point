<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Terminology;

use App\Command\Terminology\AddAnnotationCommand;
use App\CommandHandler\Terminology\AddAnnotationCommandHandler;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Form\Field;
use App\Entity\Enum\OntologyConceptType;
use App\Entity\Iri;
use App\Entity\Terminology\Annotation;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Exception\AnnotationAlreadyExists;
use App\Exception\NoAccessPermission;
use App\Exception\OntologyConceptNotFound;
use App\Exception\OntologyNotFound;
use App\Repository\OntologyConceptRepository;
use App\Security\Authorization\Voter\StudyVoter;
use Castor\BioPortal\Api\ApiWrapper;
use Castor\BioPortal\Api\Helper\SearchTermResult;
use Castor\BioPortal\Model\Concept;
use Castor\BioPortal\Model\Individual;
use Castor\BioPortal\Model\Uri;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use function array_filter;
use function array_values;

final class AddAnnotationCommandHandlerTest extends TestCase
{
    private const ONTOLOGY_ID = 'ontology-1';
    private const CONCEPT_CODE = 'C0001';

    public function testThrowsWhenUserCannotEditStudy(): void
    {
        $study = $this->study();
        $field = $this->field($study);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(StudyVoter::EDIT, $study)->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = new AddAnnotationCommandHandler($em, $this->createMock(ApiWrapper::class), $security);

        $this->expectException(NoAccessPermission::class);
        $handler($this->command($study, $field, OntologyConceptType::concept()));
    }

    public function testThrowsWhenOntologyNotFound(): void
    {
        $study = $this->study();
        $field = $this->field($study);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Ontology::class, $this->repositoryReturningFind(self::ONTOLOGY_ID, null)],
        ]);
        $em->expects(self::never())->method('flush');

        $handler = new AddAnnotationCommandHandler($em, $this->createMock(ApiWrapper::class), $this->grantedSecurity($study));

        $this->expectException(OntologyNotFound::class);
        $handler($this->command($study, $field, OntologyConceptType::concept()));
    }

    public function testReusesExistingConceptAndPersistsAnnotation(): void
    {
        $study = $this->study();
        $field = $this->field($study);
        $ontology = $this->ontology();
        $existingConcept = new OntologyConcept(new Iri('https://example.com/C0001'), self::CONCEPT_CODE, $ontology, 'Existing concept');

        $conceptRepository = $this->createMock(OntologyConceptRepository::class);
        $conceptRepository->method('findByOntologyAndCode')->with($ontology, self::CONCEPT_CODE)->willReturn($existingConcept);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Ontology::class, $this->repositoryReturningFind(self::ONTOLOGY_ID, $ontology)],
            [OntologyConcept::class, $conceptRepository],
        ]);

        $persisted = [];
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $apiWrapper = $this->createMock(ApiWrapper::class);
        $apiWrapper->expects(self::never())->method('searchTerm');
        $apiWrapper->expects(self::never())->method('searchIndividual');

        $handler = new AddAnnotationCommandHandler($em, $apiWrapper, $this->grantedSecurity($study));
        $handler($this->command($study, $field, OntologyConceptType::concept()));

        self::assertCount(1, $field->getAnnotations());
        $annotation = $field->getAnnotations()->first();
        self::assertInstanceOf(Annotation::class, $annotation);
        self::assertSame($existingConcept, $annotation->getConcept());
        // The pre-existing concept is reused, only the field and annotation get persisted.
        self::assertNotContains($existingConcept, $persisted);
        self::assertContains($field, $persisted);
    }

    public function testThrowsWhenAnnotationAlreadyExists(): void
    {
        $study = $this->study();
        $field = $this->field($study);
        $ontology = $this->ontology();
        $existingConcept = new OntologyConcept(new Iri('https://example.com/C0001'), self::CONCEPT_CODE, $ontology, 'Existing concept');
        $field->addAnnotation(new Annotation($field, $existingConcept));

        $conceptRepository = $this->createMock(OntologyConceptRepository::class);
        $conceptRepository->method('findByOntologyAndCode')->willReturn($existingConcept);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Ontology::class, $this->repositoryReturningFind(self::ONTOLOGY_ID, $ontology)],
            [OntologyConcept::class, $conceptRepository],
        ]);
        $em->expects(self::never())->method('flush');

        $handler = new AddAnnotationCommandHandler($em, $this->createMock(ApiWrapper::class), $this->grantedSecurity($study));

        $this->expectException(AnnotationAlreadyExists::class);
        $handler($this->command($study, $field, OntologyConceptType::concept()));
    }

    public function testCreatesConceptFromBioPortalConceptSearch(): void
    {
        $study = $this->study();
        $field = $this->field($study);
        $ontology = $this->ontology();

        $conceptRepository = $this->createMock(OntologyConceptRepository::class);
        $conceptRepository->method('findByOntologyAndCode')->willReturn(null);

        $bioPortalConcept = $this->createMock(Concept::class);
        $bioPortalConcept->method('getId')->willReturn(new Uri('https://example.com/onto#C0001'));
        $bioPortalConcept->method('getNotation')->willReturn('C0001');
        $bioPortalConcept->method('getPrefLabel')->willReturn('Heart rate');

        $searchResult = $this->createMock(SearchTermResult::class);
        $searchResult->method('getTotalCount')->willReturn(1);
        $searchResult->method('getCollection')->willReturn([$bioPortalConcept]);

        $apiWrapper = $this->createMock(ApiWrapper::class);
        $apiWrapper->expects(self::once())->method('searchTerm')->willReturn($searchResult);

        $persisted = [];
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Ontology::class, $this->repositoryReturningFind(self::ONTOLOGY_ID, $ontology)],
            [OntologyConcept::class, $conceptRepository],
        ]);
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $handler = new AddAnnotationCommandHandler($em, $apiWrapper, $this->grantedSecurity($study));
        $handler($this->command($study, $field, OntologyConceptType::concept()));

        $newConcepts = array_filter($persisted, static fn (object $e): bool => $e instanceof OntologyConcept);
        self::assertCount(1, $newConcepts);
        $createdConcept = array_values($newConcepts)[0];
        self::assertSame('C0001', $createdConcept->getCode());
        self::assertSame('Heart rate', $createdConcept->getDisplayName());
        $annotation = $field->getAnnotations()->first();
        self::assertInstanceOf(Annotation::class, $annotation);
        self::assertSame($createdConcept, $annotation->getConcept());
    }

    public function testThrowsWhenBioPortalConceptSearchHasNoResults(): void
    {
        $study = $this->study();
        $field = $this->field($study);
        $ontology = $this->ontology();

        $conceptRepository = $this->createMock(OntologyConceptRepository::class);
        $conceptRepository->method('findByOntologyAndCode')->willReturn(null);

        $searchResult = $this->createMock(SearchTermResult::class);
        $searchResult->method('getTotalCount')->willReturn(0);

        $apiWrapper = $this->createMock(ApiWrapper::class);
        $apiWrapper->method('searchTerm')->willReturn($searchResult);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Ontology::class, $this->repositoryReturningFind(self::ONTOLOGY_ID, $ontology)],
            [OntologyConcept::class, $conceptRepository],
        ]);
        $em->expects(self::never())->method('flush');

        $handler = new AddAnnotationCommandHandler($em, $apiWrapper, $this->grantedSecurity($study));

        $this->expectException(OntologyConceptNotFound::class);
        $handler($this->command($study, $field, OntologyConceptType::concept()));
    }

    public function testCreatesConceptFromBioPortalIndividualSearch(): void
    {
        $study = $this->study();
        $field = $this->field($study);
        $ontology = $this->ontology();

        $conceptRepository = $this->createMock(OntologyConceptRepository::class);
        $conceptRepository->method('findByOntologyAndCode')->willReturn(null);

        $individual = $this->createMock(Individual::class);
        $individual->method('getId')->willReturn(new Uri('https://example.com/onto#Ind1'));
        $individual->method('getLabel')->willReturn('Individual label');

        $apiWrapper = $this->createMock(ApiWrapper::class);
        $apiWrapper->expects(self::once())->method('searchIndividual')->willReturn([$individual]);
        $apiWrapper->expects(self::never())->method('searchTerm');

        $persisted = [];
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Ontology::class, $this->repositoryReturningFind(self::ONTOLOGY_ID, $ontology)],
            [OntologyConcept::class, $conceptRepository],
        ]);
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $handler = new AddAnnotationCommandHandler($em, $apiWrapper, $this->grantedSecurity($study));
        $handler($this->command($study, $field, OntologyConceptType::individual()));

        $newConcepts = array_filter($persisted, static fn (object $e): bool => $e instanceof OntologyConcept);
        self::assertCount(1, $newConcepts);
        $createdConcept = array_values($newConcepts)[0];
        // For individuals the code is the URI base fragment.
        self::assertSame('Ind1', $createdConcept->getCode());
        self::assertSame('Individual label', $createdConcept->getDisplayName());
    }

    public function testThrowsWhenBioPortalIndividualSearchHasNoResults(): void
    {
        $study = $this->study();
        $field = $this->field($study);
        $ontology = $this->ontology();

        $conceptRepository = $this->createMock(OntologyConceptRepository::class);
        $conceptRepository->method('findByOntologyAndCode')->willReturn(null);

        $apiWrapper = $this->createMock(ApiWrapper::class);
        $apiWrapper->method('searchIndividual')->willReturn([]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Ontology::class, $this->repositoryReturningFind(self::ONTOLOGY_ID, $ontology)],
            [OntologyConcept::class, $conceptRepository],
        ]);
        $em->expects(self::never())->method('flush');

        $handler = new AddAnnotationCommandHandler($em, $apiWrapper, $this->grantedSecurity($study));

        $this->expectException(OntologyConceptNotFound::class);
        $handler($this->command($study, $field, OntologyConceptType::individual()));
    }

    private function command(CastorStudy $study, Field $field, OntologyConceptType $type): AddAnnotationCommand
    {
        return new AddAnnotationCommand($study, $field, self::ONTOLOGY_ID, self::CONCEPT_CODE, $type);
    }

    private function study(): CastorStudy
    {
        return new CastorStudy('source-1', 'Study', 'study');
    }

    private function field(CastorStudy $study): Field
    {
        return new Field('F1', $study, null, 'Field label', null, null, null, null, null, null, null);
    }

    private function ontology(): Ontology
    {
        $ontology = new Ontology('SNOMED', new Iri('https://example.com/snomed'));
        $ontology->setBioPortalId('SNOMEDCT');

        return $ontology;
    }

    private function grantedSecurity(CastorStudy $study): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with(StudyVoter::EDIT, $study)->willReturn(true);

        return $security;
    }

    private function repositoryReturningFind(string $id, ?object $result): EntityRepository
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($id)->willReturn($result);

        return $repository;
    }
}
