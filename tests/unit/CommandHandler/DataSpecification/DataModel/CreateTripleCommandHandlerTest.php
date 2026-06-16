<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\CreateTripleCommand;
use App\CommandHandler\DataSpecification\DataModel\CreateTripleCommandHandler;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\DataSpecification\DataModel\Node\RecordNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\DataSpecification\DataModel\Triple;
use App\Entity\Enum\NodeType;
use App\Entity\Version;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\NoAccessPermission;
use App\Repository\DataSpecification\DataModel\NodeRepository;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateTripleCommandHandlerTest extends TestCase
{
    public function testThrowsWhenNotGranted(): void
    {
        $module = $this->buildModule();
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, self::isInstanceOf(DataModel::class))
            ->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = new CreateTripleCommandHandler($em, $security);

        $this->expectException(NoAccessPermission::class);
        $handler(new CreateTripleCommand($module, NodeType::value(), NodeType::record(), 'obj', 'http://p', null));
    }

    public function testCreatesTripleWithRecordSubjectAndValueObject(): void
    {
        $module = $this->buildModule();
        $version = $module->getVersion();
        self::assertInstanceOf(DataModelVersion::class, $version);

        $recordNode = new RecordNode($version);
        $valueNode = new ValueNode($version, 'Value', null);
        $valueNode->setIsAnnotatedValue(true);

        $nodeRepo = $this->createMock(NodeRepository::class);
        $nodeRepo->method('findRecordNodeForModel')->with($version)->willReturn($recordNode);
        $nodeRepo->method('findByModelAndId')->with($version, 'obj-id')->willReturn($valueNode);

        $em = $this->buildEm($nodeRepo, true, 3);
        $security = $this->grantedSecurity();

        $handler = new CreateTripleCommandHandler($em, $security);
        $handler(new CreateTripleCommand($module, NodeType::value(), NodeType::record(), 'obj-id', 'http://predicate', null));

        self::assertCount(1, $module->getElementGroups());
        $triple = $module->getElementGroups()->first();
        self::assertInstanceOf(Triple::class, $triple);
        self::assertSame($recordNode, $triple->getSubject());
        self::assertSame($valueNode, $triple->getObject());
        self::assertSame('http://predicate', $triple->getPredicate()->getIri()->getValue());
    }

    public function testResolvesNonRecordSubjectAndObjectById(): void
    {
        $module = $this->buildModule();
        $version = $module->getVersion();
        self::assertInstanceOf(DataModelVersion::class, $version);

        $subject = new ValueNode($version, 'Subject', null);
        $subject->setIsAnnotatedValue(true);
        $object = new ValueNode($version, 'Object', null);
        $object->setIsAnnotatedValue(true);

        $nodeRepo = $this->createMock(NodeRepository::class);
        $nodeRepo->method('findRecordNodeForModel')->willReturn(new RecordNode($version));
        $nodeRepo->method('findByModelAndId')->willReturnMap([
            [$version, 'subject-id', $subject],
            [$version, 'object-id', $object],
        ]);

        $em = $this->buildEm($nodeRepo, true);
        $security = $this->grantedSecurity();

        $handler = new CreateTripleCommandHandler($em, $security);
        $handler(new CreateTripleCommand($module, NodeType::value(), NodeType::value(), 'object-id', 'http://p', 'subject-id'));

        $triple = $module->getElementGroups()->first();
        self::assertInstanceOf(Triple::class, $triple);
        self::assertSame($subject, $triple->getSubject());
        self::assertSame($object, $triple->getObject());
    }

    public function testThrowsWhenValueObjectNotRepeatedInRepeatedModule(): void
    {
        $module = $this->buildModule(repeated: true);
        $version = $module->getVersion();
        self::assertInstanceOf(DataModelVersion::class, $version);

        $subject = new RecordNode($version);
        $object = new ValueNode($version, 'Object', null);
        $object->setIsAnnotatedValue(true);
        $object->setIsRepeated(false);

        $nodeRepo = $this->createMock(NodeRepository::class);
        $nodeRepo->method('findRecordNodeForModel')->willReturn($subject);
        $nodeRepo->method('findByModelAndId')->willReturn($object);

        $em = $this->buildEm($nodeRepo, false);
        $security = $this->grantedSecurity();

        $handler = new CreateTripleCommandHandler($em, $security);

        $this->expectException(InvalidNodeType::class);
        $handler(new CreateTripleCommand($module, NodeType::value(), NodeType::record(), 'obj-id', 'http://p', null));
    }

    public function testAllowsRepeatedValueObjectInRepeatedModule(): void
    {
        $module = $this->buildModule(repeated: true);
        $version = $module->getVersion();
        self::assertInstanceOf(DataModelVersion::class, $version);

        $subject = new RecordNode($version);
        $object = new ValueNode($version, 'Object', null);
        $object->setIsAnnotatedValue(true);
        $object->setIsRepeated(true);

        $nodeRepo = $this->createMock(NodeRepository::class);
        $nodeRepo->method('findRecordNodeForModel')->willReturn($subject);
        $nodeRepo->method('findByModelAndId')->willReturn($object);

        $em = $this->buildEm($nodeRepo, true);
        $security = $this->grantedSecurity();

        $handler = new CreateTripleCommandHandler($em, $security);
        $handler(new CreateTripleCommand($module, NodeType::value(), NodeType::record(), 'obj-id', 'http://p', null));

        self::assertCount(1, $module->getElementGroups());
    }

    private function grantedSecurity(): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, self::isInstanceOf(DataModel::class))
            ->willReturn(true);

        return $security;
    }

    private function buildEm(NodeRepository $nodeRepo, bool $expectFlush, ?int $expectedPersists = null): EntityManagerInterface
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->with(Node::class)->willReturn($nodeRepo);

        if ($expectFlush) {
            $em->expects(self::once())->method('flush');
        } else {
            $em->expects(self::never())->method('flush');
        }

        if ($expectedPersists !== null) {
            $em->expects(self::exactly($expectedPersists))->method('persist');
        }

        return $em;
    }

    private function buildModule(bool $repeated = false): DataModelGroup
    {
        $model = new DataModel('Model', null);
        $version = new DataModelVersion(new Version('1.0.0'));
        $model->addVersion($version);

        return new DataModelGroup('Module', 1, $repeated, false, $version);
    }
}
