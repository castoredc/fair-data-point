<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelFieldCommand;
use App\CommandHandler\DataSpecification\MetadataModel\CreateMetadataModelFieldCommandHandler;
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

final class CreateMetadataModelFieldCommandHandlerTest extends TestCase
{
    public function testThrowsWhenNotGranted(): void
    {
        $form = $this->buildForm();
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::once())->method('isGranted')->willReturn(false);
        $em->expects(self::never())->method('flush');

        $handler = new CreateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NoAccessPermission::class);
        $handler($this->buildCommand($form, MetadataFieldType::input(), null));
    }

    public function testCreatesFieldWithPlainNode(): void
    {
        $form = $this->buildForm();
        $node = $this->buildValueNode($form->getMetadataModelVersion());

        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn($node);

        $em = $this->buildEm($nodeRepo, null, true);
        $security = $this->grantedSecurity();

        $handler = new CreateMetadataModelFieldCommandHandler($em, $security);
        $handler($this->buildCommand($form, MetadataFieldType::input(), null));

        self::assertCount(1, $form->getFields());
        $field = $form->getFields()->last();
        self::assertInstanceOf(MetadataModelField::class, $field);
        self::assertSame('Field title', $field->getTitle());
        self::assertSame($node, $field->getNode());
    }

    public function testResolvesOptionGroupWhenFieldTypeRequiresIt(): void
    {
        $form = $this->buildForm();
        $version = $form->getMetadataModelVersion();
        $node = $this->buildValueNode($version);
        $optionGroup = new MetadataModelOptionGroup($version, 'Options', null);

        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn($node);
        $optionGroupRepo = $this->createMock(EntityRepository::class);
        $optionGroupRepo->method('find')->with('og-id')->willReturn($optionGroup);

        $em = $this->buildEm($nodeRepo, $optionGroupRepo, true);
        $security = $this->grantedSecurity();

        $handler = new CreateMetadataModelFieldCommandHandler($em, $security);
        $handler($this->buildCommand($form, MetadataFieldType::dropdown(), 'og-id'));

        $field = $form->getFields()->last();
        self::assertInstanceOf(MetadataModelField::class, $field);
        self::assertSame($optionGroup, $field->getOptionGroup());
    }

    public function testThrowsNotFoundWhenOptionGroupMissing(): void
    {
        $form = $this->buildForm();
        $optionGroupRepo = $this->createMock(EntityRepository::class);
        $optionGroupRepo->method('find')->with('og-id')->willReturn(null);

        $em = $this->buildEm($this->createMock(EntityRepository::class), $optionGroupRepo, false);
        $security = $this->grantedSecurity();

        $handler = new CreateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NotFound::class);
        $handler($this->buildCommand($form, MetadataFieldType::dropdown(), 'og-id'));
    }

    public function testThrowsNotFoundWhenNodeMissing(): void
    {
        $form = $this->buildForm();
        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn(null);

        $em = $this->buildEm($nodeRepo, null, false);
        $security = $this->grantedSecurity();

        $handler = new CreateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NotFound::class);
        $handler($this->buildCommand($form, MetadataFieldType::input(), null));
    }

    public function testThrowsNodeAlreadyUsed(): void
    {
        $form = $this->buildForm();
        $version = $form->getMetadataModelVersion();
        $node = $this->buildValueNode($version);

        // Attach an existing field to the node so hasField() returns true.
        $existingForm = new MetadataModelForm('Existing', 1, ResourceType::catalog(), $version);
        $existingField = new MetadataModelField('Existing field', null, 1, $node, MetadataFieldType::input(), null, ResourceType::catalog(), false, $existingForm);
        $this->setPrivate($node, ValueNode::class, 'field', $existingField);

        $nodeRepo = $this->createMock(EntityRepository::class);
        $nodeRepo->method('find')->with('node-id')->willReturn($node);

        $em = $this->buildEm($nodeRepo, null, false);
        $security = $this->grantedSecurity();

        $handler = new CreateMetadataModelFieldCommandHandler($em, $security);

        $this->expectException(NodeAlreadyUsed::class);
        $handler($this->buildCommand($form, MetadataFieldType::input(), null));
    }

    private function buildCommand(MetadataModelForm $form, MetadataFieldType $type, ?string $optionGroup): CreateMetadataModelFieldCommand
    {
        return new CreateMetadataModelFieldCommand(
            $form,
            'Field title',
            'Field description',
            1,
            'node-id',
            $type,
            $optionGroup,
            ResourceType::catalog(),
            false
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

    private function buildForm(): MetadataModelForm
    {
        $model = new MetadataModel('Model', null);
        $version = new MetadataModelVersion(new Version('1.0.0'));
        $model->addVersion($version);

        return new MetadataModelForm('Form', 1, ResourceType::catalog(), $version);
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
