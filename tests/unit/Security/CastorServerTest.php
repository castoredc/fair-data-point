<?php
declare(strict_types=1);

namespace App\Tests\unit\Security;

use App\Entity\Encryption\EncryptedString;
use App\Security\CastorServer;
use App\Service\EncryptionService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CastorServerTest extends TestCase
{
    public function testCanCreateNewNonDefaultCastorServer(): void
    {
        $uri = 'https://td1.castoredc.org';
        $name = 'My next server';
        $flag = 'nl';

        $newServer = CastorServer::nonDefaultServer($uri, $name, $flag);

        self::assertSame($uri, (string) $newServer->getUrl());
        self::assertSame($name, $newServer->getName());
        self::assertSame($flag, $newServer->getFlag());
        self::assertFalse($newServer->isDefault());
    }

    public function testCanCreateNewDefaultCastorServer(): void
    {
        $uri = 'https://td1.castoredc.org';
        $name = 'My next server';
        $flag = 'nl';

        $newServer = CastorServer::defaultServer($uri, $name, $flag);

        self::assertSame($uri, (string) $newServer->getUrl());
        self::assertSame($name, $newServer->getName());
        self::assertSame($flag, $newServer->getFlag());
        self::assertTrue($newServer->isDefault());
    }

    /** @dataProvider invalidUrls */
    public function testCannotCreateCastorServerWithInvalidURL(string $uri): void
    {
        $this->expectException(InvalidArgumentException::class);
        $name = 'My next server';
        $flag = 'nl';

        $newServer = CastorServer::defaultServer($uri, $name, $flag);

        self::assertSame($uri, (string) $newServer->getUrl());
        self::assertSame($name, $newServer->getName());
        self::assertSame($flag, $newServer->getFlag());
        self::assertTrue($newServer->isDefault());
    }

    /** @return array<array<string>> */
    public function invalidUrls(): array
    {
        return [
            ['some random text'],
            ['12310231'],
            ['null'],
            [''],
        ];
    }

    public function testCanAddClientCredentialsToExistingDatabaseServer(): void
    {
        $uri = 'https://td1.castoredc.org';
        $name = 'My next server';
        $flag = 'nl';

        $server = CastorServer::defaultServer($uri, $name, $flag);
        $clientId = 'E47AFBFF-A602-43EF-8F7C-F14616BCFE3F';
        $clientSecret = '555d8f0fbb944546e626869a92cdfcdf';

        $encryption = $this->createMock(EncryptionService::class);
        $encryption->expects(self::exactly(2))
            ->method('encrypt')
            ->willReturnOnConsecutiveCalls(
                new EncryptedString('encryptedClientId', 'nonce1'),
                new EncryptedString('encryptedClientSecret', 'nonce2')
            );

        $serverWithCredentials = CastorServer::withClientCredentials($server, $encryption, $clientId, $clientSecret);

        self::assertSame('{"cipherText":"encryptedClientId","nonce":"nonce1"}', $serverWithCredentials->getClientIdCiphertext());
        self::assertSame('{"cipherText":"encryptedClientSecret","nonce":"nonce2"}', $serverWithCredentials->getClientSecretCiphertext());
    }
}
