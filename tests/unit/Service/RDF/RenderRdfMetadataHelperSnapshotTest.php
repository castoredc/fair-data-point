<?php
declare(strict_types=1);

namespace App\Tests\unit\Service\RDF;

use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\ChildrenNode;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\LiteralNode;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ParentsNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\DataSpecification\MetadataModel\Predicate;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\XsdDataType;
use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\Iri;
use App\Entity\Metadata\DatasetMetadata;
use App\Entity\Metadata\DistributionMetadata;
use App\Entity\Metadata\Metadata;
use App\Entity\Metadata\MetadataValue;
use App\Entity\Version as VersionNumber;
use App\Service\RDF\RenderRdfMetadataHelper;
use App\Service\UriHelper;
use App\Tests\Helper\RdfSnapshotAssertions;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use EasyRdf\Graph;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Bundle\SecurityBundle\Security;
use function json_encode;
use const JSON_THROW_ON_ERROR;

/**
 * Golden-file tests for RenderRdfMetadataHelper: a realistic DCAT metadata
 * model is built in memory and rendered for a dataset and a distribution.
 * The committed fixtures in tests/fixtures/rdf/ guard the EasyRdf Turtle and
 * JSON-LD serialisation output across dependency upgrades.
 */
final class RenderRdfMetadataHelperSnapshotTest extends TestCase
{
    use RdfSnapshotAssertions;

    private const BASE_URI = 'https://fdp.example.com';

    public function testRendersDatasetMetadata(): void
    {
        $dataset = self::dataset();

        self::assertGraphMatchesSnapshots(
            self::helper()->renderEntity($dataset, new Graph()),
            'metadata/dataset'
        );
    }

    public function testRendersDistributionMetadata(): void
    {
        $distribution = self::distribution();

        self::assertGraphMatchesSnapshots(
            self::helper()->renderEntity($distribution, new Graph()),
            'metadata/distribution'
        );
    }

    public function testRenderingTheSameEntityTwiceIsDeterministic(): void
    {
        $dataset = self::dataset();
        $helper = self::helper();

        $first = $helper->renderEntity($dataset, new Graph())->serialise('turtle');
        $second = $helper->renderEntity($dataset, new Graph())->serialise('turtle');

        self::assertSame($first, $second);
    }

    private function helper(): RenderRdfMetadataHelper
    {
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

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn(true);

        return new RenderRdfMetadataHelper(
            self::createStub(EntityManagerInterface::class),
            $uriHelper,
            $security
        );
    }

    /**
     * A dataset with a parent catalog and a child distribution, described by a
     * DCAT-style metadata model covering every node type the helper supports:
     * record, literal (fixed and placeholder), value (langString), internal
     * IRI, external IRI, children and parents nodes.
     */
    private static function dataset(): Dataset
    {
        $model = new MetadataModelVersion(new VersionNumber('1.0.0'));
        $module = new MetadataModelGroup('Dataset', 1, ResourceType::dataset(), $model);
        $model->addGroup($module);

        $datasetNode = new RecordNode($model, ResourceType::dataset());

        $typeNode = new ExternalIriNode($model, 'Dataset type', null);
        $typeNode->setIri(new Iri('http://www.w3.org/ns/dcat#Dataset'));

        $titleNode = new ValueNode($model, 'Title', null);
        $titleNode->setDataType(XsdDataType::langString());

        $licenseNode = new LiteralNode($model, 'License', null);
        $licenseNode->setValue('https://creativecommons.org/licenses/by/4.0/');
        $licenseNode->setDataType(XsdDataType::url());

        $issuedNode = new LiteralNode($model, 'Issued', null);
        $issuedNode->setValue('##created_at##');
        $issuedNode->setDataType(XsdDataType::dateTime());

        $modifiedNode = new LiteralNode($model, 'Modified', null);
        $modifiedNode->setValue('##updated_at##');
        $modifiedNode->setDataType(XsdDataType::dateTime());

        $versionNode = new LiteralNode($model, 'Version', null);
        $versionNode->setValue('##metadata_version##');
        $versionNode->setDataType(XsdDataType::string());

        $identifierNode = new InternalIriNode($model, 'Identifier', null);
        $identifierNode->setSlug('identifier');

        $distributionsNode = new ChildrenNode($model, ResourceType::distribution());
        $catalogsNode = new ParentsNode($model, ResourceType::catalog());

        self::addTriple($module, $datasetNode, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $typeNode);
        self::addTriple($module, $datasetNode, 'http://purl.org/dc/terms/title', $titleNode);
        self::addTriple($module, $datasetNode, 'http://purl.org/dc/terms/license', $licenseNode);
        self::addTriple($module, $datasetNode, 'http://purl.org/dc/terms/issued', $issuedNode);
        self::addTriple($module, $datasetNode, 'http://purl.org/dc/terms/modified', $modifiedNode);
        self::addTriple($module, $datasetNode, 'http://purl.org/dc/terms/hasVersion', $versionNode);
        self::addTriple($module, $datasetNode, 'http://purl.org/dc/terms/identifier', $identifierNode);
        self::addTriple($module, $datasetNode, 'http://www.w3.org/ns/dcat#distribution', $distributionsNode);
        self::addTriple($module, $datasetNode, 'http://www.w3.org/ns/dcat#catalog', $catalogsNode);

        $dataset = new Dataset('cardiology-trial');
        $dataset->setCatalogs(new ArrayCollection(['main-catalog' => new Catalog('main-catalog')]));
        $dataset->addDistribution(new Distribution('rdf-distribution', $dataset));

        $metadata = new DatasetMetadata($dataset);
        self::configureMetadata($metadata, $model);

        $metadata->addValue(new MetadataValue($metadata, $titleNode, json_encode([
            ['text' => 'Cardiology Trial', 'language' => 'en'],
            ['text' => 'Cardiologie Studie', 'language' => 'nl'],
        ], JSON_THROW_ON_ERROR)));

        $dataset->addMetadata($metadata);

        return $dataset;
    }

    /**
     * A distribution with RDF contents, described by a metadata model that
     * uses the distribution-specific access URL and media type placeholders.
     */
    private static function distribution(): Distribution
    {
        $model = new MetadataModelVersion(new VersionNumber('2.1.0'));
        $module = new MetadataModelGroup('Distribution', 1, ResourceType::distribution(), $model);
        $model->addGroup($module);

        $distributionNode = new RecordNode($model, ResourceType::distribution());

        $typeNode = new ExternalIriNode($model, 'Distribution type', null);
        $typeNode->setIri(new Iri('http://www.w3.org/ns/dcat#Distribution'));

        $accessUrlNode = new LiteralNode($model, 'Access URL', null);
        $accessUrlNode->setValue('##distribution_access_url##');
        $accessUrlNode->setDataType(XsdDataType::url());

        $mediaTypeNode = new LiteralNode($model, 'Media type', null);
        $mediaTypeNode->setValue('##distribution_media_type##');
        $mediaTypeNode->setDataType(XsdDataType::string());

        $resourceUrlNode = new LiteralNode($model, 'Resource URL', null);
        $resourceUrlNode->setValue('##resource_url##');
        $resourceUrlNode->setDataType(XsdDataType::url());

        self::addTriple($module, $distributionNode, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', $typeNode);
        self::addTriple($module, $distributionNode, 'http://www.w3.org/ns/dcat#accessURL', $accessUrlNode);
        self::addTriple($module, $distributionNode, 'http://www.w3.org/ns/dcat#mediaType', $mediaTypeNode);
        self::addTriple($module, $distributionNode, 'http://purl.org/dc/terms/identifier', $resourceUrlNode);

        $dataset = new Dataset('cardiology-trial');
        $distribution = new Distribution('rdf-distribution', $dataset);
        $distribution->setContents(new RDFDistribution($distribution));

        $metadata = new DistributionMetadata($distribution);
        self::configureMetadata($metadata, $model);

        $distribution->addMetadata($metadata);

        return $distribution;
    }

    private static function addTriple(MetadataModelGroup $module, Node $subject, string $predicateIri, Node $object): void
    {
        $predicate = new Predicate($subject->getMetadataModelVersion(), new Iri($predicateIri));

        $module->addTriple(new Triple($module, $subject, $predicate, $object));
    }

    private static function configureMetadata(Metadata $metadata, MetadataModelVersion $model): void
    {
        $metadata->setVersion(new VersionNumber('1.0.0'));
        $metadata->setMetadataModelVersion($model);

        // createdAt/updatedAt are normally set by Doctrine lifecycle callbacks;
        // freeze them so the placeholder rendering is deterministic.
        self::setPrivateMetadataProperty($metadata, 'createdAt', new DateTimeImmutable('2024-05-01T12:30:00+00:00'));
        self::setPrivateMetadataProperty($metadata, 'updatedAt', new DateTime('2024-06-15T08:45:30+00:00'));
    }

    private static function setPrivateMetadataProperty(Metadata $metadata, string $property, mixed $value): void
    {
        $reflection = new ReflectionProperty(Metadata::class, $property);
        $reflection->setValue($metadata, $value);
    }
}
