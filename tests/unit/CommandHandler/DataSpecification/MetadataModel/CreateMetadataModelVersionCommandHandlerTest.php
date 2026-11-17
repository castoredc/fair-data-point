<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelVersionCommand;
use App\CommandHandler\DataSpecification\MetadataModel\CreateMetadataModelVersionCommandHandler;
use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;
use App\Entity\DataSpecification\MetadataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\MetadataModel\Node\RecordNode;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\DataSpecification\MetadataModel\Predicate;
use App\Entity\DataSpecification\MetadataModel\Triple;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\VersionType;
use App\Entity\Iri;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use App\Service\VersionNumberHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateMetadataModelVersionCommandHandlerTest extends TestCase
{
    public function testThrowsWhenNotGranted(): void
    {
        $model = new MetadataModel('Model', null);
        $model->addVersion(new MetadataModelVersion(new Version('1.0.0')));

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $model)
            ->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = new CreateMetadataModelVersionCommandHandler($em, $security, new VersionNumberHelper());

        $this->expectException(NoAccessPermission::class);
        $handler(new CreateMetadataModelVersionCommand($model, VersionType::minor()));
    }

    public function testDuplicatesLatestVersionAndBumpsVersionNumber(): void
    {
        $model = $this->buildModelWithContent();

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->grantedSecurity($model);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = new CreateMetadataModelVersionCommandHandler($em, $security, new VersionNumberHelper());
        $newVersion = $handler(new CreateMetadataModelVersionCommand($model, VersionType::major()));

        self::assertSame('2.0.0', $newVersion->getVersion()->getValue());

        // The model now has two versions, the latest being the new one.
        self::assertCount(2, $model->getVersions());
        self::assertSame($newVersion, $model->getLatestVersion());

        // Prefixes are copied.
        self::assertCount(1, $newVersion->getPrefixes());

        // All three nodes (record + external IRI + value) copied.
        self::assertCount(3, $newVersion->getElements());

        // Predicate copied.
        self::assertCount(1, $newVersion->getPredicates());

        // Module with its triple copied.
        self::assertCount(1, $newVersion->getGroups());
        $module = $newVersion->getGroups()->first();
        self::assertInstanceOf(MetadataModelGroup::class, $module);
        self::assertCount(1, $module->getElementGroups());
    }

    public function testMinorBumpProducesPatchZero(): void
    {
        $model = $this->buildModelWithContent('1.2.3');

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->grantedSecurity($model);

        $handler = new CreateMetadataModelVersionCommandHandler($em, $security, new VersionNumberHelper());
        $newVersion = $handler(new CreateMetadataModelVersionCommand($model, VersionType::minor()));

        self::assertSame('1.3.0', $newVersion->getVersion()->getValue());
    }

    private function grantedSecurity(MetadataModel $model): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $model)
            ->willReturn(true);

        return $security;
    }

    private function buildModelWithContent(string $versionNumber = '1.0.0'): MetadataModel
    {
        $model = new MetadataModel('Model', null);
        $version = new MetadataModelVersion(new Version($versionNumber));
        $model->addVersion($version);

        $version->addPrefix(new NamespacePrefix('dct', new Iri('http://purl.org/dc/terms/')));

        $subject = new RecordNode($version, ResourceType::catalog());
        $this->setId($subject, 'node-subject');
        $version->addNode($subject);

        $object = new ExternalIriNode($version, 'External', null);
        $object->setIri(new Iri('http://example.org/thing'));
        $this->setId($object, 'node-object');
        $version->addNode($object);

        $valueNode = new ValueNode($version, 'Value', null);
        $valueNode->setIsAnnotatedValue(true);
        $this->setId($valueNode, 'node-value');
        $version->addNode($valueNode);

        $predicate = new Predicate($version, new Iri('http://purl.org/dc/terms/title'));
        $this->setId($predicate, 'predicate-1', CommonPredicate::class);
        $version->addPredicate($predicate);

        $module = new MetadataModelGroup('Module', 1, ResourceType::catalog(), $version);
        $triple = new Triple($module, $subject, $predicate, $object);
        $module->addElementGroup($triple);
        $version->setGroups(new ArrayCollection([$module]));

        return $model;
    }

    /** @param class-string $declaringClass */
    private function setId(object $entity, string $id, string $declaringClass = Element::class): void
    {
        $reflection = new ReflectionProperty($declaringClass, 'id');
        $reflection->setAccessible(true);
        $reflection->setValue($entity, $id);
    }
}
