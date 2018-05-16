<?php
declare(strict_types=1);

namespace App\Model;

use League\OAuth2\Client\Provider\GenericProvider;

class UnverifiedSSLOAuthClient extends GenericProvider {
    protected function getAllowedClientOptions( array $options ) {
        return [ 'timeout', 'proxy', 'verify' ];
    }
}
