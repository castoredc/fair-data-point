<?php
declare(strict_types=1);

namespace App\Security\Providers\Orcid;

use App\Security\Providers\Authenticator;
use App\Security\User;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use function assert;

class OrcidAuthenticator extends Authenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'oauth_orcid_check';
    }

    /** @inheritDoc */
    public function getUser($credentials)
    {
        $orcidUser = $this->getOrcidClient()->fetchUserFromToken($credentials);
        assert($orcidUser instanceof OrcidUser);

        $dbUser = $this->em->getRepository(OrcidUser::class)->findOneBy(['orcid' => $orcidUser->getId()]);

        if ($dbUser === null) {
            // No Orcid User found in database, create new User and attach Castor User to it

            $user = $this->currentUser ?? $this->createNewUser($orcidUser);
            $user->setOrcid($orcidUser);
            $orcidUser->setUser($user);
        } else {
            $this->detectIfEqualToLoggedInUser($dbUser);

            // Orcid User Found, add token to user from DB

            $dbUser->setToken($orcidUser->getToken());
            $dbUser->setName($orcidUser->getName());
            $user = $dbUser->getUser();
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function createNewUser(OrcidUser $orcidUser): User
    {
        return new User(null);
    }

    private function getOrcidClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('orcid');
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

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_FORBIDDEN);
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->fetchAccessToken($this->getOrcidClient());
        $user = $this->getUser($accessToken);

        return new SelfValidatingPassport(new UserBadge($accessToken->getToken(), $user->getUserIdentifier()));
    }
}
