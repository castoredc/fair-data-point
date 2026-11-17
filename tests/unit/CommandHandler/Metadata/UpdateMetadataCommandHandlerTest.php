<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Metadata;

use App\Command\Metadata\UpdateMetadataCommand;
use App\CommandHandler\Metadata\UpdateMetadataCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroupOption;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\XsdDataType;
use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Metadata\Metadata;
use App\Entity\Metadata\MetadataValue;
use App\Entity\Terminology\Ontology;
use App\Entity\Terminology\OntologyConcept;
use App\Exception\InvalidMetadataValue;
use App\Exception\NoAccessPermission;
use App\Exception\OntologyNotFound;
use App\Repository\OntologyConceptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use function json_decode;

final class UpdateMetadataCommandHandlerTest extends TestCase
{
    public function testThrowsWhenUserCannotEditEntity(): void
    {
        $entity = $this->createMock(MetadataEnrichedEntity::class);

        $metadata = $this->createMock(Metadata::class);
        $metadata->method('getEntity')->willReturn($entity);
        $metadata->method('getMetadataModelVersion')->willReturn($this->createMock(MetadataModelVersion::class));

        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with('edit', $entity)
            ->willReturn(false);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('flush');

        $handler = new UpdateMetadataCommandHandler($em, $security);

        $this->expectException(NoAccessPermission::class);

        $handler(new UpdateMetadataCommand($metadata, []));
    }

    public function testPlainStringValueIsJsonEncodedAndPersisted(): void
    {
        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(false);
        $node->method('getDataType')->willReturn(XsdDataType::string());

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::input());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $persisted = [];
        $em->method('persist')->willReturnCallback(static function (object $entity) use (&$persisted): void {
            $persisted[] = $entity;
        });
        $em->expects(self::once())->method('flush');

        $metadata->expects(self::once())->method('onUpdate');

        $addedValues = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $value) use (&$addedValues): void {
            $addedValues[] = $value;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'hello']));

        self::assertCount(1, $addedValues);
        self::assertSame('"hello"', $addedValues[0]->getValue());
        self::assertContains($addedValues[0], $persisted);
        self::assertContains($metadata, $persisted);
    }

    /** @return iterable<string, array{mixed}> */
    public static function emptyValueProvider(): iterable
    {
        yield 'empty string' => [''];
        yield 'empty array' => [[]];
        yield 'null' => [null];
    }

    #[DataProvider('emptyValueProvider')]
    public function testEmptyValuesAreStoredAsEmptyString(mixed $value): void
    {
        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(false);
        $node->method('getDataType')->willReturn(XsdDataType::string());

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::input());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);
        $em->expects(self::once())->method('flush');

        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => $value]));

        self::assertSame('', $added[0]->getValue());
    }

    public function testExistingMetadataValuesAreRemovedBeforeAddingNew(): void
    {
        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(false);
        $node->method('getDataType')->willReturn(XsdDataType::string());

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::input());

        $existingValue = $this->createMock(MetadataValue::class);

        [$em, $security, $metadata] = $this->buildHandlerCollaborators(
            [$field],
            allowEdit: true,
            existingValues: [$existingValue],
        );

        $removed = [];
        $em->method('remove')->willReturnCallback(static function (object $entity) use (&$removed): void {
            $removed[] = $entity;
        });
        $em->expects(self::once())->method('flush');
        $metadata->method('addValue');

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'value']));

        self::assertSame([$existingValue], $removed);
    }

    public function testLangStringValueIsParsedAndEncoded(): void
    {
        $language = new Language();
        $language->setCode('en');
        $language->setName('English');

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(false);
        $node->method('getDataType')->willReturn(XsdDataType::langString());

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::inputLocale());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $languageRepository = $this->createMock(EntityRepository::class);
        $languageRepository->method('find')->with('en')->willReturn($language);
        $this->wireRepositories($em, [Language::class => $languageRepository]);

        $em->expects(self::once())->method('flush');

        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, [
            'field-1' => [['text' => 'Hello', 'language' => 'en']],
        ]));

        $decoded = json_decode($added[0]->getValue(), true);
        self::assertIsArray($decoded);
        self::assertSame('Hello', $decoded[0]['text']);
        self::assertSame('en', $decoded[0]['language']);
    }

    public function testCountryPickerResolvesCode(): void
    {
        $country = $this->createMock(Country::class);
        $country->method('getCode')->willReturn('NL');

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::countryPicker());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $countryRepository = $this->createMock(EntityRepository::class);
        $countryRepository->method('find')->with('NL')->willReturn($country);
        $this->wireRepositories($em, [Country::class => $countryRepository]);

        $em->expects(self::once())->method('flush');
        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'NL']));

        self::assertSame('"NL"', $added[0]->getValue());
    }

    public function testCountryPickerThrowsWhenCountryNotFound(): void
    {
        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::countryPicker());
        $field->method('getTitle')->willReturn('Country field');

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $countryRepository = $this->createMock(EntityRepository::class);
        $countryRepository->method('find')->willReturn(null);
        $this->wireRepositories($em, [Country::class => $countryRepository]);

        $em->expects(self::never())->method('flush');

        $handler = new UpdateMetadataCommandHandler($em, $security);

        $this->expectException(InvalidMetadataValue::class);

        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'XX']));
    }

    public function testLicensePickerResolvesSlug(): void
    {
        $license = $this->createMock(License::class);
        $license->method('getSlug')->willReturn('cc-by');

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::licensePicker());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $licenseRepository = $this->createMock(EntityRepository::class);
        $licenseRepository->method('find')->with('cc-by')->willReturn($license);
        $this->wireRepositories($em, [License::class => $licenseRepository]);

        $em->expects(self::once())->method('flush');
        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'cc-by']));

        self::assertSame('"cc-by"', $added[0]->getValue());
    }

    public function testLanguagePickerResolvesCode(): void
    {
        $language = new Language();
        $language->setCode('fr');
        $language->setName('French');

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::languagePicker());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $languageRepository = $this->createMock(EntityRepository::class);
        $languageRepository->method('find')->with('fr')->willReturn($language);
        $this->wireRepositories($em, [Language::class => $languageRepository]);

        $em->expects(self::once())->method('flush');
        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'fr']));

        self::assertSame('"fr"', $added[0]->getValue());
    }

    public function testDropdownResolvesSingleOption(): void
    {
        $option = $this->createMock(MetadataModelOptionGroupOption::class);
        $option->method('getValue')->willReturn('resolved-value');

        $optionGroup = $this->createMock(MetadataModelOptionGroup::class);
        $optionGroup->method('getOption')->with('input-value')->willReturn($option);

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::dropdown());
        $field->method('getOptionGroup')->willReturn($optionGroup);

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);
        $em->expects(self::once())->method('flush');
        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'input-value']));

        self::assertSame('"resolved-value"', $added[0]->getValue());
    }

    public function testDropdownThrowsWhenOptionMissing(): void
    {
        $optionGroup = $this->createMock(MetadataModelOptionGroup::class);
        $optionGroup->method('getOption')->willReturn(null);

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::dropdown());
        $field->method('getOptionGroup')->willReturn($optionGroup);
        $field->method('getTitle')->willReturn('Dropdown field');

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);
        $em->expects(self::never())->method('flush');

        $handler = new UpdateMetadataCommandHandler($em, $security);

        $this->expectException(InvalidMetadataValue::class);

        $handler(new UpdateMetadataCommand($metadata, ['field-1' => 'unknown']));
    }

    public function testCheckboxesResolveMultipleOptions(): void
    {
        $optionA = $this->createMock(MetadataModelOptionGroupOption::class);
        $optionA->method('getValue')->willReturn('a-value');
        $optionB = $this->createMock(MetadataModelOptionGroupOption::class);
        $optionB->method('getValue')->willReturn('b-value');

        $optionGroup = $this->createMock(MetadataModelOptionGroup::class);
        $optionGroup->method('getOption')->willReturnMap([
            ['a', $optionA],
            ['b', $optionB],
        ]);

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::checkboxes());
        $field->method('getOptionGroup')->willReturn($optionGroup);

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);
        $em->expects(self::once())->method('flush');
        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, ['field-1' => ['a', 'b']]));

        $decoded = json_decode($added[0]->getValue(), true);
        self::assertSame(['a-value', 'b-value'], $decoded);
    }

    public function testOntologyConceptBrowserUsesExistingConcept(): void
    {
        $ontology = $this->createMock(Ontology::class);

        $dbConcept = $this->createMock(OntologyConcept::class);
        $dbConcept->method('toArray')->willReturn(['code' => 'C1', 'displayName' => 'Concept 1']);

        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::ontologyConceptBrowser());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $ontologyRepository = $this->createMock(EntityRepository::class);
        $ontologyRepository->method('find')->with('ont-1')->willReturn($ontology);

        $conceptRepository = $this->createMock(OntologyConceptRepository::class);
        $conceptRepository->method('findByOntologyAndCode')->with($ontology, 'C1')->willReturn($dbConcept);

        $this->wireRepositories($em, [
            Ontology::class => $ontologyRepository,
            OntologyConcept::class => $conceptRepository,
        ]);

        $em->expects(self::once())->method('flush');
        $added = [];
        $metadata->method('addValue')->willReturnCallback(static function (MetadataValue $v) use (&$added): void {
            $added[] = $v;
        });

        $handler = new UpdateMetadataCommandHandler($em, $security);
        $handler(new UpdateMetadataCommand($metadata, [
            'field-1' => [
                [
                    'ontology' => ['id' => 'ont-1'],
                    'code' => 'C1',
                    'url' => 'http://example.com/c1',
                    'displayName' => 'Concept 1',
                ],
            ],
        ]));

        $decoded = json_decode($added[0]->getValue(), true);
        self::assertSame('C1', $decoded[0]['code']);
    }

    public function testOntologyConceptBrowserThrowsWhenOntologyMissing(): void
    {
        $node = $this->createMock(ValueNode::class);
        $node->method('isAnnotatedValue')->willReturn(true);

        $field = $this->createMock(MetadataModelField::class);
        $field->method('getId')->willReturn('field-1');
        $field->method('getNode')->willReturn($node);
        $field->method('getFieldType')->willReturn(MetadataFieldType::ontologyConceptBrowser());

        [$em, $security, $metadata] = $this->buildHandlerCollaborators([$field], allowEdit: true);

        $ontologyRepository = $this->createMock(EntityRepository::class);
        $ontologyRepository->method('find')->willReturn(null);
        $conceptRepository = $this->createMock(OntologyConceptRepository::class);

        $this->wireRepositories($em, [
            Ontology::class => $ontologyRepository,
            OntologyConcept::class => $conceptRepository,
        ]);

        $em->expects(self::never())->method('flush');

        $handler = new UpdateMetadataCommandHandler($em, $security);

        $this->expectException(OntologyNotFound::class);

        $handler(new UpdateMetadataCommand($metadata, [
            'field-1' => [
                [
                    'ontology' => ['id' => 'missing'],
                    'code' => 'C1',
                    'url' => 'http://example.com/c1',
                    'displayName' => 'Concept 1',
                ],
            ],
        ]));
    }

    /**
     * @param array<MetadataModelField> $fields
     * @param array<MetadataValue>      $existingValues
     *
     * @return array{EntityManagerInterface&MockObject, Security&MockObject, Metadata&MockObject}
     */
    private function buildHandlerCollaborators(array $fields, bool $allowEdit, array $existingValues = []): array
    {
        $entity = $this->createMock(MetadataEnrichedEntity::class);

        $modelVersion = $this->createMock(MetadataModelVersion::class);
        $modelVersion->method('getFields')->willReturn(new ArrayCollection($fields));

        $metadata = $this->createMock(Metadata::class);
        $metadata->method('getEntity')->willReturn($entity);
        $metadata->method('getMetadataModelVersion')->willReturn($modelVersion);
        $metadata->method('getValues')->willReturn(new ArrayCollection($existingValues));

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->with('edit', $entity)->willReturn($allowEdit);

        $em = $this->createMock(EntityManagerInterface::class);

        return [$em, $security, $metadata];
    }

    /**
     * @param EntityManagerInterface                        &MockObject   $em
     * @param array<class-string, EntityRepository<object>> $repositories
     */
    private function wireRepositories(MockObject $em, array $repositories): void
    {
        $em->method('getRepository')->willReturnCallback(
            static function (string $class) use ($repositories): EntityRepository {
                return $repositories[$class];
            }
        );
    }
}
