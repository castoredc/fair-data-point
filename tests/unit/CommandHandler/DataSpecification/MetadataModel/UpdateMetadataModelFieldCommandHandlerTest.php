<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelFieldCommand;
use App\CommandHandler\DataSpecification\MetadataModel\UpdateMetadataModelFieldCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\ResourceType;
use App\Entity\Version;
use App\Exception\DataSpecification\MetadataModel\NodeAlreadyUsed;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Bundle\SecurityBundle\Security;

final class UpdateMetadataModelFieldCommandHandlerTest extends TestCase
{
    public function testThrowsWhenNotGranted(): void
    {
        $field = $this->buildField();
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())->method('isGranted')->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = new UpdateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NoAccessPermission::class);
        $handler($this->buildCommand($field, MetadataFieldType::input(), null));
    }

    public function testUpdatesFieldWithNewNodeAndValues(): void
    {
        $field = $this->buildField();
        $version = $field->getForm()->getMetadataModelVersion();
        $newNode = $this->buildValueNode($version);

        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn($newNode);

        $em = $this->buildEm($nodeRepo, null, true);
        $security = $this->grantedSecurity();

        $handler = new UpdateMetadataModelFieldCommandHandler($em, $security);
        $handler($this->buildCommand($field, MetadataFieldType::input(), null, 'New title'));

        self::assertSame('New title', $field->getTitle());
        self::assertSame($newNode, $field->getNode());
    }

    public function testThrowsNotFoundWhenNodeIsNotAValueNode(): void
    {
        $field = $this->buildField();
        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn(null);

        $em = $this->buildEm($nodeRepo, null, false);
        $security = $this->grantedSecurity();

        $handler = new UpdateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NotFound::class);
        $handler($this->buildCommand($field, MetadataFieldType::input(), null));
    }

    public function testThrowsNotFoundWhenOptionGroupMissing(): void
    {
        $field = $this->buildField();
        $optionGroupRepo = $this->createMock(EntityRepository::class);
        $optionGroupRepo->method('find')->with('og-id')->willReturn(null);

        $em = $this->buildEm($this->createMock(EntityRepository::class), $optionGroupRepo, false);
        $security = $this->grantedSecurity();

        $handler = new UpdateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NotFound::class);
        $handler($this->buildCommand($field, MetadataFieldType::dropdown(), 'og-id'));
    }

    public function testAllowsReusingTheSameNodeAlreadyOnThisField(): void
    {
        $field = $this->buildField();
        $node = $field->getNode();

        // Point the node's field back at the field being updated.
        $this->setPrivate($node, ValueNode::class, 'field', $field);

        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn($node);

        $em = $this->buildEm($nodeRepo, null, true);
        $security = $this->grantedSecurity();

        $handler = new UpdateMetadataModelFieldCommandHandler($em, $security);
        $handler($this->buildCommand($field, MetadataFieldType::input(), null, 'Renamed'));

        self::assertSame('Renamed', $field->getTitle());
    }

    public function testThrowsNodeAlreadyUsedByAnotherField(): void
    {
        $field = $this->buildField();
        $version = $field->getForm()->getMetadataModelVersion();
        $otherNode = $this->buildValueNode($version);

        $otherForm = new MetadataModelForm('Other', 1, ResourceType::catalog(), $version);
        $otherField = new MetadataModelField('Other field', null, 1, $otherNode, MetadataFieldType::input(), null, ResourceType::catalog(), false, $otherForm);
        $this->setPrivate($otherNode, ValueNode::class, 'field', $otherField);

        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn($otherNode);

        $em = $this->buildEm($nodeRepo, null, false);
        $security = $this->grantedSecurity();

        $handler = new UpdateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NodeAlreadyUsed::class);
        $handler($this->buildCommand($field, MetadataFieldType::input(), null));
    }

    private function buildCommand(MetadataModelField $field, MetadataFieldType $type, ?string $optionGroup, string $title = 'Title'): UpdateMetadataModelFieldCommand
    {
        return new UpdateMetadataModelFieldCommand(
            $field,
            $title,
            'Description',
            1,
            'node-id',
            $type,
            $optionGroup,
            ResourceType::catalog(),
            true
        );
    }

    private function grantedSecurity(): Security
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')
            ->with(DataSpecificationVoter::EDIT, self::isInstanceOf(MetadataModel::class))
            ->willReturn(true);

        return $security;
    }

    private function buildEm(EntityRepository $nodeRepo, ?EntityRepository $optionGroupRepo, bool $expectFlush): EntityManagerInterface
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnCallback(
            static function (string $class) use ($nodeRepo, $optionGroupRepo): EntityRepository {
                if ($class === MetadataModelOptionGroup::class && $optionGroupRepo !== null) {
                    return $optionGroupRepo;
                }

                return $nodeRepo;
            }
        );

        if ($expectFlush) {
            $em->expects(self::once())->method('flush');
        } else {
            $em->expects(self::never())->method('flush');
        }

        return $em;
    }

    private function buildField(): MetadataModelField
    {
        $model = new MetadataModel('Model', null);
        $version = new MetadataModelVersion(new Version('1.0.0'));
        $model->addVersion($version);

        $form = new MetadataModelForm('Form', 1, ResourceType::catalog(), $version);
        $node = $this->buildValueNode($version);

        return new MetadataModelField('Original', null, 1, $node, MetadataFieldType::input(), null, ResourceType::catalog(), false, $form);
    }

    private function buildValueNode(MetadataModelVersion $version): ValueNode
    {
        $node = new ValueNode($version, 'Value', null);
        $node->setIsAnnotatedValue(true);

        return $node;
    }

    /** @param class-string $declaringClass */
    private function setPrivate(object $entity, string $declaringClass, string $property, mixed $value): void
    {
        $reflection = new ReflectionProperty($declaringClass, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($entity, $value);
    }
}
