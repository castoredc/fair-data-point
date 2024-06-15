<?php
declare(strict_types=1);

namespace App\Security\Providers\Castor;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\NameOrigin;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Security\Providers\Authenticator;
use App\Security\User;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use function assert;
use function http_build_query;
use function strtr;

class CastorAuthenticator extends Authenticator
{
    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_castor_check';
    }

    public function getUser(AccessToken $credentials): User
    {
        $castorUser = $this->getCastorClient()->fetchUserFromToken($credentials);
        assert($castorUser instanceof CastorUser);

        $dbUser = $this->em->getRepository(CastorUser::class)->findOneBy(['id' => $castorUser->getId()]);
        assert($dbUser instanceof CastorUser || $dbUser === null);

        $this->detectIfEqualToLoggedInUser($dbUser);

        if ($dbUser === null) {
            // No Castor User found in database, create new User and attach Castor User to it

            $user = $this->currentUser ?? $this->createNewUser($castorUser);
            $user->setCastorUser($castorUser);
            $castorUser->setUser($user);
        } else {
            // Castor User Found, update user

            $dbUser->setNameFirst($castorUser->getNameFirst());
            $dbUser->setNameMiddle($castorUser->getNameMiddle());
            $dbUser->setNameLast($castorUser->getNameLast());

            $dbUser->setToken($castorUser->getToken());
            $dbUser->setServer($castorUser->getServer());

            $user = $dbUser->getUser();
        }

        $this->apiClient->setUser($user->getCastorUser());

        $user->getCastorUser()->setStudies($this->apiClient->getStudyIds());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->fetchAccessToken($this->getCastorClient());
        $user = $this->getUser($accessToken);

        return new SelfValidatingPassport(new UserBadge($user->getId()));
    }

    private function createNewUser(CastorUser $castorUser): User
    {
        $person = new Person(
            $castorUser->getNameFirst(),
            $castorUser->getNameMiddle(),
            $castorUser->getNameLast(),
            $castorUser->getEmailAddress(),
            null,
            null,
            NameOrigin::castor()
        );

        return new User($person);
    }

    private function getCastorClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('castor');
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
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /** @inheritDoc */
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
                $catalog = $request->attributes->get('catalog');
            } else {
                $catalog = $this->em->getRepository(Catalog::class)->findOneBy(['slug' => $request->attributes->get('catalog')]);
            }

            assert($catalog instanceof Catalog || $catalog === null);

            if ($catalog !== null && $catalog->isAcceptingSubmissions()) {
                $url .= '/' . $catalog->getSlug();
            }
        }

        if ($request->attributes->has('dataset')) {
            $dataset = null;
            $params['view'] = 'dataset';

            if ($request->attributes->get('dataset') instanceof Dataset) {
                $dataset = $request->attributes->get('dataset');
            } else {
                $dataset = $this->em->getRepository(Dataset::class)->findOneBy(['slug' => $request->attributes->get('dataset')]);
            }

            assert($dataset instanceof Dataset || $dataset === null);

            $study = $dataset?->getStudy();

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
