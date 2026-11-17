<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request;

use App\Validator\Constraints\Slug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function http_build_query;
use function json_encode;
use const JSON_THROW_ON_ERROR;

/**
 * Helpers for testing ApiRequest objects the way ApiController::parseRequest uses them:
 * the request object is constructed from an HTTP request and then passed to the validator.
 *
 * The validator returned by {@see createValidator()} replaces the database-backed
 * Slug constraint validator with a no-op, so tests exercise only the request object's
 * own parsing and constraints. The Slug validator itself is covered by SlugValidatorTest.
 */
trait ApiRequestTestHelpers
{
    private static function createValidator(): ValidatorInterface
    {
        $factory = new class extends ConstraintValidatorFactory {
            public function getInstance(Constraint $constraint): ConstraintValidatorInterface
            {
                if ($constraint instanceof Slug) {
                    return new class extends ConstraintValidator {
                        public function validate(mixed $value, Constraint $constraint): void
                        {
                        }
                    };
                }

                return parent::getInstance($constraint);
            }
        };

        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->setConstraintValidatorFactory($factory)
            ->getValidator();
    }

    /** @param array<mixed> $data */
    private static function createJsonRequest(array $data): Request
    {
        return Request::create(
            'https://fdp.castoredc.local',
            Request::METHOD_POST,
            [],
            [],
            [],
            [],
            json_encode($data, JSON_THROW_ON_ERROR)
        );
    }

    /** @param array<mixed> $query */
    private static function createQueryRequest(array $query): Request
    {
        return Request::create(
            'https://fdp.castoredc.local?' . http_build_query($query),
            Request::METHOD_GET
        );
    }

    /** @return list<string> */
    private static function getViolatedPropertyPaths(ConstraintViolationListInterface $violations): array
    {
        $paths = [];

        foreach ($violations as $violation) {
            $paths[] = $violation->getPropertyPath();
        }

        return $paths;
    }
}
