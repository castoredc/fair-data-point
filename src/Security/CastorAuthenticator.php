<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Castor\CastorStudy;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function assert;
use function http_build_query;
use function strtr;

class CastorAuthenticator extends SocialAuthenticator
{
    /** @var ClientRegistry */
    private $clientRegistry;

    /** @var EntityManagerInterface */
    private $em;

    /** @var RouterInterface */
    private $router;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient, ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->apiClient = $apiClient;
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_castor_check';
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getCastorClient());
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var CastorUser $castorUser */
        $castorUser = $this->getCastorClient()
            ->fetchUserFromToken($credentials);

        /** @var CastorUser|null $user */
        $user = $this->em->getRepository(CastorUser::class)
            ->findOneBy(['id' => $castorUser->getId()]);

        if ($user === null) {
            $user = $castorUser;
        } else {
            $user->setToken($castorUser->getToken());
            $user->setServer($castorUser->getServer());
        }

        $this->apiClient->setUser($user);

        $user->setStudies($this->apiClient->getStudyIds());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function getCastorClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry
            ->getClient('castor');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        $url = $this->router->generate('fdp');
        $previous = $request->getSession()->get('previous');

        if ($previous !== null) {
            $url = $previous;
        }

        return new RedirectResponse($url);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        $url = '/login';

        $params = [
            'path' => $request->getRequestUri(),
        ];

        if ($request->attributes->has('catalog')) {
            $catalog = null;
            $params['view'] = 'catalog';

            if ($request->attributes->get('catalog') instanceof Catalog) {
                /** @var Catalog $catalog */
                $catalog = $request->attributes->get('catalog');
            } else {
                $catalog = $this->em->getRepository(Catalog::class)->findOneBy(['slug' => $request->attributes->get('catalog')]);
            }

            if ($catalog !== null && $catalog->isAcceptingSubmissions()) {
                $url .= '/' . $catalog->getSlug();
            }
        }

        if ($request->attributes->has('dataset')) {
            $dataset = null;
            $params['view'] = 'dataset';

            if ($request->attributes->get('dataset') instanceof Dataset) {
                /** @var Dataset $dataset */
                $dataset = $request->attributes->get('dataset');
            } else {
                $dataset = $this->em->getRepository(Dataset::class)->findOneBy(['slug' => $request->attributes->get('dataset')]);
            }

            $study = $dataset !== null ? $dataset->getStudy() : null;

            if ($study !== null) {
                assert($study instanceof CastorStudy);

                $params['server'] = $study->getServer()->getId();
                $params['serverLocked'] = true;
            }
        }

        if ($request->attributes->has('distribution')) {
            $dataset = null;
            $params['view'] = 'distribution';
        }

        return new RedirectResponse(
            $url . '?' . http_build_query($params),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
