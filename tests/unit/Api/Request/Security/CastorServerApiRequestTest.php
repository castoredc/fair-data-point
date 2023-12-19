<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Request\Security;

use App\Api\Request\Security\CastorServerApiRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final class CastorServerApiRequestTest extends TestCase
{
    public function testCanProcessValidRequest(): void
    {
        $request = $this->getRequestWithData(
            [
                'id' => 1,
                'name' => 'My EDC Test server',
                'url' => 'https://el.stupido.org/',
                'flag' => 'es',
                'default' => true,
                'clientId' => 'ABCDEF123123123123',
                'clientSecret' => 'lkjhfdaqweuioafpiubdfjnjw',
            ]
        );

        $apiRequest = new CastorServerApiRequest($request);

        self::assertSame(1, $apiRequest->getId());
        self::assertSame('My EDC Test server', $apiRequest->getName());
        self::assertSame('https://el.stupido.org/', $apiRequest->getUrl());
        self::assertSame('es', $apiRequest->getFlag());
        self::assertSame('ABCDEF123123123123', $apiRequest->getClientId());
        self::assertSame('lkjhfdaqweuioafpiubdfjnjw', $apiRequest->getClientSecret());
    }

    /** @param array<mixed> $data */
    private function getRequestWithData(array $data): Request
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
}
