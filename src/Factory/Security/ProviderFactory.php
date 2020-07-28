<?php
declare(strict_types=1);

namespace App\Factory\Security;

use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProviderFactory
{
    /** @var UrlGeneratorInterface */
    private $generator;

    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(UrlGeneratorInterface $generator, EntityManagerInterface $em, ApiClient $apiClient)
    {
        $this->generator = $generator;
        $this->em = $em;
        $this->apiClient = $apiClient;
    }

    /**
     * @inheritDoc
     */
    public function createProvider(string $class, array $options, string $redirectUri, array $redirectParams = [], array $collaborators = [])
    {
        $redirectUri = $this->generator->generate($redirectUri, $redirectParams, UrlGeneratorInterface::ABSOLUTE_URL);

        $options['redirectUri'] = $redirectUri;

        return new $class($this->em, $this->apiClient, $options, $collaborators);
    }
}
