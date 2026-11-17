<?php
declare(strict_types=1);

namespace App\Tests\unit\Validator\Constraints;

use App\Api\Request\ApiRequest;
use App\Entity\FAIRData\Catalog;
use App\Validator\Constraints\Slug;
use App\Validator\Constraints\SlugValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/** @extends ConstraintValidatorTestCase<SlugValidator> */
final class SlugValidatorTest extends ConstraintValidatorTestCase
{
    private EntityManagerInterface&MockObject $em;

    protected function createValidator(): SlugValidator
    {
        $this->em = $this->createMock(EntityManagerInterface::class);

        return new SlugValidator($this->em);
    }

    public function testThrowsForWrongConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('some-slug', new NotBlank());
    }

    public function testNoViolationForUniqueSlug(): void
    {
        $this->setObject($this->createApiRequest());
        $this->expectRepositoryReturns('my-unique-slug', []);

        $this->validator->validate('my-unique-slug', new Slug(type: Catalog::class));

        $this->assertNoViolation();
    }

    public function testViolationForDuplicateSlug(): void
    {
        $this->setObject($this->createApiRequest());
        $this->expectRepositoryReturns('existing-slug', [new stdClass()]);

        $this->validator->validate('existing-slug', new Slug(type: Catalog::class));

        $this->buildViolation('This slug is already in use, please use a different slug')
            ->assertRaised();
    }

    #[DataProvider('provideForbiddenSlugs')]
    public function testViolationForForbiddenSlug(string $forbiddenSlug): void
    {
        $this->setObject($this->createApiRequest());
        $this->expectRepositoryReturns($forbiddenSlug, []);

        $this->validator->validate($forbiddenSlug, new Slug(type: Catalog::class));

        $this->buildViolation('This slug cannot be used, please use a different slug')
            ->assertRaised();
    }

    /** @return array<string, array{string}> */
    public static function provideForbiddenSlugs(): array
    {
        return [
            'add' => ['add'],
            'delete' => ['delete'],
            'remove' => ['remove'],
            'import' => ['import'],
            'catalog' => ['catalog'],
            'dataset' => ['dataset'],
            'distribution' => ['distribution'],
            'study' => ['study'],
        ];
    }

    public function testNoViolationWhenSlugIsUnchangedForCurrentContext(): void
    {
        $this->setObject($this->createApiRequest($this->createContextEntityWithSlug('my-slug')));
        $this->em->expects(self::never())->method('getRepository');

        $this->validator->validate('my-slug', new Slug(type: Catalog::class));

        $this->assertNoViolation();
    }

    public function testUniquenessIsCheckedWhenSlugDiffersFromCurrentContext(): void
    {
        $this->setObject($this->createApiRequest($this->createContextEntityWithSlug('old-slug')));
        $this->expectRepositoryReturns('new-slug', [new stdClass()]);

        $this->validator->validate('new-slug', new Slug(type: Catalog::class));

        $this->buildViolation('This slug is already in use, please use a different slug')
            ->assertRaised();
    }

    private function createApiRequest(?object $context = null): ApiRequest
    {
        $apiRequest = new class (null, new ParameterBag()) extends ApiRequest {
            protected function parse(): void
            {
            }
        };

        if ($context !== null) {
            $apiRequest->setContext($context);
        }

        return $apiRequest;
    }

    private function createContextEntityWithSlug(string $slug): object
    {
        return new class ($slug) {
            public function __construct(private string $slug)
            {
            }

            public function getSlug(): string
            {
                return $this->slug;
            }
        };
    }

    /** @param array<object> $foundEntities */
    private function expectRepositoryReturns(string $slug, array $foundEntities): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects(self::once())
            ->method('findBy')
            ->with(['slug' => $slug])
            ->willReturn($foundEntities);

        $this->em->expects(self::once())
            ->method('getRepository')
            ->with(Catalog::class)
            ->willReturn($repository);
    }
}
