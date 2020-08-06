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
use Symfony\Component\Security\Core\User\UserProviderInterface;
use function preg_replace;
use function strpos;
use function trim;

class OrcidAuthenticator extends Authenticator
{
    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'oauth_orcid_check';
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getOrcidClient());
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var OrcidUser $orcidUser */
        $orcidUser = $this->getOrcidClient()->fetchUserFromToken($credentials);

        /** @var OrcidUser|null $dbUser */
        $dbUser = $this->em->getRepository(OrcidUser::class)->findOneBy(['orcid' => $orcidUser->getId()]);

        $this->detectIfEqualToLoggedInUser($dbUser);

        if ($dbUser === null) {
            // No Orcid User found in database, create new User and attach Castor User to it

            $user = $this->currentUser ?? $this->createNewUser($orcidUser);
            $user->setOrcid($orcidUser);
            $orcidUser->setUser($user);
        } else {
            // Orcid User Found, add token to user from DB

            $dbUser->setToken($orcidUser->getToken());
            $user = $dbUser->getUser();
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function createNewUser(OrcidUser $orcidUser): User
    {
        $name = trim($orcidUser->getName());
        $lastName = strpos($name, ' ') === false ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $firstName = trim(preg_replace('#' . $lastName . '#', '', $name));

        return new User($firstName, null, $lastName, null);
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

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response($exception->getMessage(), Response::HTTP_FORBIDDEN);
    }
}
