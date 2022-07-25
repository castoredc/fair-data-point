<?php
declare(strict_types=1);

namespace App\Tests\unit\Security;

use App\Security\CastorServer;
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
}
