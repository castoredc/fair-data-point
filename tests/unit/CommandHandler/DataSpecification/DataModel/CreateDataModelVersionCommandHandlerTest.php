<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\CreateDataModelVersionCommand;
use App\CommandHandler\DataSpecification\DataModel\CreateDataModelVersionCommandHandler;
use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Model\Predicate as CommonPredicate;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\NamespacePrefix;
use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\RecordNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\DataSpecification\DataModel\Predicate;
use App\Entity\DataSpecification\DataModel\Triple;
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

final class CreateDataModelVersionCommandHandlerTest extends TestCase
{
    public function testThrowsWhenNotGranted(): void
    {
        $model = new DataModel('Model', null);
        $model->addVersion(new DataModelVersion(new Version('1.0.0')));

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $model)
            ->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = new CreateDataModelVersionCommandHandler($em, $security, new VersionNumberHelper());

        $this->expectException(NoAccessPermission::class);
        $handler(new CreateDataModelVersionCommand($model, VersionType::minor()));
    }

    public function testDuplicatesLatestVersionAndBumpsVersionNumber(): void
    {
        $model = $this->buildModelWithContent();

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->grantedSecurity($model);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = new CreateDataModelVersionCommandHandler($em, $security, new VersionNumberHelper());
        $newVersion = $handler(new CreateDataModelVersionCommand($model, VersionType::patch()));

        self::assertSame('1.0.1', $newVersion->getVersion()->getValue());
        self::assertCount(2, $model->getVersions());
        self::assertSame($newVersion, $model->getLatestVersion());

        self::assertCount(1, $newVersion->getPrefixes());
        self::assertCount(3, $newVersion->getElements());
        self::assertCount(1, $newVersion->getPredicates());

        self::assertCount(1, $newVersion->getGroups());
        $module = $newVersion->getGroups()->first();
        self::assertInstanceOf(DataModelGroup::class, $module);
        self::assertCount(1, $module->getElementGroups());
    }

    public function testMajorBumpResetsMinorAndPatch(): void
    {
        $model = $this->buildModelWithContent('2.4.6');

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->grantedSecurity($model);

        $handler = new CreateDataModelVersionCommandHandler($em, $security, new VersionNumberHelper());
        $newVersion = $handler(new CreateDataModelVersionCommand($model, VersionType::major()));

        self::assertSame('3.0.0', $newVersion->getVersion()->getValue());
    }

    private function grantedSecurity(DataModel $model): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $model)
            ->willReturn(true);

        return $security;
    }

    private function buildModelWithContent(string $versionNumber = '1.0.0'): DataModel
    {
        $model = new DataModel('Model', null);
        $version = new DataModelVersion(new Version($versionNumber));
        $model->addVersion($version);

        $version->addPrefix(new NamespacePrefix('dct', new Iri('http://purl.org/dc/terms/')));

        $subject = new RecordNode($version);
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

        $module = new DataModelGroup('Module', 1, false, false, $version);
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
