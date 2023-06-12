<?php
declare(strict_types=1);

namespace App\Tests\unit\Api\Resource\Security;

use App\Api\Resource\Security\CastorServerApiResource;
use App\Security\CastorServer;
use App\Service\EncryptionService;
use PHPUnit\Framework\TestCase;

final class CastorServerApiResourceTest extends TestCase
{
    public function testCanSerializeCastorServerToArray(): void
    {
        $server = CastorServer::defaultServer('https://td1.castoredc.org', 'TD1', 'nl');
        $expected = [
            'id' => null,
            'url' => 'https://td1.castoredc.org',
            'name' => 'TD1',
            'flag' => 'nl',
            'default' => true,
        ];

        $resource = new CastorServerApiResource($server, false, $this->createMock(EncryptionService::class));
        self::assertSame($expected, $resource->toArray());
    }

    public function testCanSerializeClientDataForAdminUsers(): void
    {
        $uri = 'https://td1.castoredc.org';
        $name = 'TD1';
        $flag = 'nl';

        $server = CastorServer::defaultServer($uri, $name, $flag);
        $clientId = 'E47AFBFF-A602-43EF-8F7C-F14616BCFE3F';
        $clientSecret = '555d8f0fbb944546e626869a92cdfcdf';

        $encryption = new EncryptionService('3264363739616265383861636561373735336333623437373464623233666365');
        $server->updateClientCredentials($encryption, $clientId, $clientSecret);

        $expected = [
            'id' => null,
            'url' => 'https://td1.castoredc.org',
            'name' => 'TD1',
            'flag' => 'nl',
            'default' => true,
            'client_id' => 'E47AFBFF-A602-43EF-8F7C-F14616BCFE3F',
            'client_secret' => '555d8f0fbb944546e626869a92cdfcdf',
        ];

        $resource = new CastorServerApiResource($server, true, $encryption);
        self::assertSame($expected, $resource->toArray());
    }
}
