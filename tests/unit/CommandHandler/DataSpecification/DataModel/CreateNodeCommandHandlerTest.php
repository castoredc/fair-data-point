<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\CreateNodeCommand;
use App\CommandHandler\DataSpecification\DataModel\CreateNodeCommandHandler;
use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\DataSpecification\DataModel\Node\ExternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\InternalIriNode;
use App\Entity\DataSpecification\DataModel\Node\LiteralNode;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;
use App\Entity\Version;
use App\Exception\DataSpecification\Common\Model\InvalidNodeType;
use App\Exception\DataSpecification\Common\Model\InvalidValueType;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateNodeCommandHandlerTest extends TestCase
{
    public function testThrowsWhenNotGranted(): void
    {
        $version = $this->buildVersion();
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())
            ->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $version->getDataModel())
            ->willReturn(false);

        $em->expects(self::never())->method('flush');

        $handler = new CreateNodeCommandHandler($em, $security);

        $this->expectException(NoAccessPermission::class);
        $handler(new CreateNodeCommand($version, NodeType::externalIri(), 'Title', 'http://x', null, null, null));
    }

    public function testCreatesExternalIriNode(): void
    {
        [$handler, $version] = $this->buildHandler();

        $handler(new CreateNodeCommand($version, NodeType::externalIri(), 'External', 'http://example.org', 'desc', null, null));

        $node = $version->getElements()->last();
        self::assertInstanceOf(ExternalIriNode::class, $node);
        self::assertSame('http://example.org', $node->getIri()?->getValue());
    }

    public function testCreatesInternalIriNode(): void
    {
        [$handler, $version] = $this->buildHandler();

        $handler(new CreateNodeCommand($version, NodeType::internalIri(), 'Internal', 'slug', null, null, true));

        $node = $version->getElements()->last();
        self::assertInstanceOf(InternalIriNode::class, $node);
        self::assertSame('slug', $node->getSlug());
        self::assertTrue($node->isRepeated());
    }

    public function testCreatesLiteralNode(): void
    {
        [$handler, $version] = $this->buildHandler();

        $handler(new CreateNodeCommand($version, NodeType::literal(), 'Literal', 'hi', null, XsdDataType::string(), null));

        $node = $version->getElements()->last();
        self::assertInstanceOf(LiteralNode::class, $node);
        self::assertSame('hi', $node->getValue());
    }

    public function testCreatesAnnotatedValueNode(): void
    {
        [$handler, $version] = $this->buildHandler();

        $handler(new CreateNodeCommand($version, NodeType::value(), 'Value', 'annotated', null, null, true));

        $node = $version->getElements()->last();
        self::assertInstanceOf(ValueNode::class, $node);
        self::assertTrue($node->isAnnotatedValue());
        self::assertTrue($node->isRepeated());
    }

    public function testCreatesPlainValueNode(): void
    {
        [$handler, $version] = $this->buildHandler();

        $handler(new CreateNodeCommand($version, NodeType::value(), 'Value', 'plain', null, XsdDataType::string(), false));

        $node = $version->getElements()->last();
        self::assertInstanceOf(ValueNode::class, $node);
        self::assertFalse($node->isAnnotatedValue());
        self::assertFalse($node->isRepeated());
        self::assertTrue($node->getDataType()?->isString());
    }

    public function testThrowsInvalidValueTypeForUnknownValue(): void
    {
        [$handler, $version] = $this->buildHandler(expectFlush: false);

        $this->expectException(InvalidValueType::class);
        $handler(new CreateNodeCommand($version, NodeType::value(), 'Value', 'nonsense', null, null, false));
    }

    public function testThrowsInvalidNodeTypeForRecord(): void
    {
        [$handler, $version] = $this->buildHandler(expectFlush: false);

        $this->expectException(InvalidNodeType::class);
        $handler(new CreateNodeCommand($version, NodeType::record(), 'Title', 'value', null, null, null));
    }

    /** @return array{CreateNodeCommandHandler, DataModelVersion} */
    private function buildHandler(bool $expectFlush = true): array
    {
        $version = $this->buildVersion();
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, $version->getDataModel())
            ->willReturn(true);

        if ($expectFlush) {
            $em->expects(self::once())->method('flush');
            $em->expects(self::exactly(2))->method('persist');
        } else {
            $em->expects(self::never())->method('flush');
        }

        return [new CreateNodeCommandHandler($em, $security), $version];
    }

    private function buildVersion(): DataModelVersion
    {
        $model = new DataModel('Model', null);
        $version = new DataModelVersion(new Version('1.0.0'));
        $model->addVersion($version);

        return $version;
    }
}
