<?php
declare(strict_types=1);

namespace App\Factory\Security;

use App\Model\Castor\ApiClient;
use App\Security\Providers\Castor\CastorUserProvider;
use App\Security\Providers\Orcid\OrcidUserProvider;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ProviderFactory
{
    public function __construct(
        private UrlGeneratorInterface $generator,
        private EntityManagerInterface $em,
        private ApiClient $apiClient,
        private EncryptionService $encryptionService,
    ) {
    }

    /**
     * @param mixed[] $options
     * @param mixed[] $redirectParams
     * @param mixed[] $collaborators
     */
    public function createProvider(
        string $class,
        array $options,
        string $redirectUri,
        array $redirectParams = [],
        array $collaborators = [],
    ): UserProviderInterface {
        $options['redirectUri'] = $this->generator->generate(
            $redirectUri,
            $redirectParams,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        if ($class === CastorUserProvider::class) {
            return new CastorUserProvider(
                $this->em,
                $this->apiClient,
                $this->encryptionService,
                $options,
                $collaborators
            );
        }

        if ($class === OrcidUserProvider::class) {
            return new OrcidUserProvider($this->em, $options, $collaborators);
        }

        throw new InvalidTypeException();
    }
}
