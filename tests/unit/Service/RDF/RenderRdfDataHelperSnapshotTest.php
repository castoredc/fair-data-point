<?php
declare(strict_types=1);

namespace App\Tests\unit\Service\RDF;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Data\FieldResult;
use App\Entity\Castor\Data\RecordDataCollection;
use App\Entity\Castor\Data\StudyData;
use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Institute;
use App\Entity\Castor\Record;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\Common\Mapping\ElementMapping;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\LiteralNode;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Node\RecordNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\DataSpecification\DataModel\Predicate;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Entity\Enum\XsdDataType;
use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\Country;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\Iri;
use App\Entity\Version as VersionNumber;
use App\Model\Castor\ApiClient;
use App\Model\Castor\CastorEntityCollection;
use App\Service\CastorEntityHelper;
use App\Service\DataTransformationService;
use App\Service\RDF\RenderRdfDataHelper;
use App\Service\UriHelper;
use App\Tests\Helper\RdfSnapshotAssertions;
use DateTimeImmutable;
use EasyRdf\Graph;
use PHPUnit\Framework\TestCase;

/**
 * Golden-file tests for RenderRdfDataHelper: a small Castor study data model
 * is built in memory and a record is rendered to RDF. The committed fixtures
 * in tests/fixtures/rdf/ guard the EasyRdf Turtle and JSON-LD serialisation
 * output across dependency upgrades.
 */
final class RenderRdfDataHelperSnapshotTest extends TestCase
{
    use RdfSnapshotAssertions;

    private const BASE_URI = 'https://fdp.example.com';

    public function testRendersRecord(): void
    {
        [$helper, $record] = $this->helperAndRecord();

        self::assertGraphMatchesSnapshots(
            $helper->renderRecord($record, new Graph()),
            'data/record'
        );
    }

    public function testRenderingTheSameRecordTwiceIsDeterministic(): void
    {
        [$helper, $record] = $this->helperAndRecord();

        $first = $helper->renderRecord($record, new Graph())->serialise('turtle');
        $second = $helper->renderRecord($record, new Graph())->serialise('turtle');

        self::assertSame($first, $second);
    }

    /** @return array{RenderRdfDataHelper, Record} */
    private function helperAndRecord(): array
    {
        $study = new CastorStudy('castor-study-1', 'Cardiology Trial', 'cardiology-trial');

        $dataModel = new DataModel('Patient data model', null);
        $version = new DataModelVersion(new VersionNumber('1.0.0'));
        $dataModel->addVersion($version);

        $module = new DataModelGroup('Study variables', 1, false, false, $version);
        $version->addGroup($module);

        $recordNode = new RecordNode($version);

        $typeNode = new ExternalIriNode($version, 'Patient type', null);
        $typeNode->setIri(new Iri('http://purl.obolibrary.org/obo/NCIT_C16960'));

        $recordIdNode = new LiteralNode($version, 'Record id', null);
        $recordIdNode->setValue('##record_id##');
        $recordIdNode->setDataType(XsdDataType::string());

        $instituteNameNode = new LiteralNode($version, 'Institute name', null);
        $instituteNameNode->setValue('##institute_name##');
        $instituteNameNode->setDataType(XsdDataType::string());

        $countryCodeNode = new LiteralNode($version, 'Institute country code', null);
        $countryCodeNode->setValue('##institute_country_code##');
        $countryCodeNode->setDataType(XsdDataType::string());

        $heightNode = new ValueNode($version, 'Height', null);
        $heightNode->setDataType(XsdDataType::decimal());

        $birthDateNode = new ValueNode($version, 'Birth date', null);
        $birthDateNode->setDataType(XsdDataType::date());

        $measurementNode = new InternalIriNode($version, 'Measurement', null);
        $measurementNode->setSlug('measurement');

        self::addTriple($module, $recordNode, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $typeNode);
        self::addTriple($module, $recordNode, 'http://purl.org/dc/terms/identifier', $recordIdNode);
        self::addTriple($module, $recordNode, 'http://example.org/fdp/instituteName', $instituteNameNode);
        self::addTriple($module, $recordNode, 'http://example.org/fdp/instituteCountry', $countryCodeNode);
        self::addTriple($module, $recordNode, 'http://example.org/fdp/hasMeasurement', $measurementNode);
        self::addTriple($module, $measurementNode, 'http://example.org/fdp/height', $heightNode);
        self::addTriple($module, $recordNode, 'http://example.org/fdp/birthDate', $birthDateNode);

        $dataset = new Dataset('cardiology-trial');
        $dataset->setStudy($study);

        $distribution = new Distribution('rdf-distribution', $dataset);
        $contents = new RDFDistribution($distribution);
        $distribution->setContents($contents);
        $contents->setDataModel($dataModel);
        $contents->setCurrentDataModelVersion($version);

        $heightField = new Field('field-height', $study, 'numeric', 'Height', null, 'height', null, null, null, 'cm', null);
        $birthDateField = new Field('field-birth-date', $study, 'date', 'Birth date', null, 'birth_date', null, null, null, null, null);

        self::addMapping($study, $version, $heightNode, $heightField);
        self::addMapping($study, $version, $birthDateNode, $birthDateField);

        $record = self::record($study);
        $studyData = new StudyData($record);
        $studyData->addData(new FieldResult($heightField, '182.5', $record));
        $studyData->addData(new FieldResult($birthDateField, '1985-05-15', $record));

        $data = new RecordDataCollection($record);
        $data->setStudyData($studyData);
        $record->setData($data);

        $uriHelper = $this->createMock(UriHelper::class);
        $uriHelper->method('getBaseUri')->willReturn(self::BASE_URI);
        $uriHelper
            ->method('getUri')
            ->willReturnCallback(static function (mixed $object, bool $addTrailingSlash = false): ?string {
                if (! $object instanceof AccessibleEntity) {
                    return null;
                }

                return self::BASE_URI . $object->getRelativeUrl() . ($addTrailingSlash ? '/' : '');
            });

        $helper = new RenderRdfDataHelper(
            $distribution,
            self::createStub(ApiClient::class),
            self::createStub(CastorEntityHelper::class),
            $uriHelper,
            self::createStub(DataTransformationService::class),
            $study,
            new CastorEntityCollection()
        );

        return [$helper, $record];
    }

    private static function record(CastorStudy $study): Record
    {
        $country = new Country();
        $country->setCode('NL');
        $country->setName('Netherlands');
        $country->setAbbreviation('NLD');
        $country->setTld('.nl');
        $country->setCastorCountryId('155');

        $institute = new Institute($study, 'institute-1', 'Example Hospital', 'EH', 'EH-01', 155);
        $institute->setCountry($country);

        return new Record(
            $study,
            $institute,
            '110001',
            new DateTimeImmutable('2024-01-10T09:00:00+00:00'),
            new DateTimeImmutable('2024-02-20T16:30:00+00:00')
        );
    }

    private static function addTriple(DataModelGroup $module, Node $subject, string $predicateIri, Node $object): void
    {
        $version = $module->getVersion();
        self::assertInstanceOf(DataModelVersion::class, $version);

        $predicate = new Predicate($version, new Iri($predicateIri));

        $module->addTriple(new Triple($module, $subject, $predicate, $object));
    }

    private static function addMapping(CastorStudy $study, DataModelVersion $version, Node $node, Field $field): void
    {
        $mapping = new ElementMapping($study, $node, $version);
        $mapping->addEntity($field);

        $study->getMappings()->add($mapping);
    }
}
