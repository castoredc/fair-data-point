<?php
declare(strict_types=1);

namespace App\Security\Providers;

use App\Entity\Castor\CastorStudy;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Exception\Security\CurrentUserAlreadyHasAttachedProviderUser;
use App\Model\Castor\ApiClient;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use function assert;
use function http_build_query;

abstract class Authenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    protected ClientRegistry $clientRegistry;
    protected EntityManagerInterface $em;
    protected RouterInterface $router;
    protected ApiClient $apiClient;
    protected ?User $currentUser = null;

    public function __construct(ApiClient $apiClient, ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, Security $security)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->apiClient = $apiClient;

        $user = $security->getUser();
        assert($user === null || $user instanceof User);
        $this->currentUser = $user;
    }

    protected function detectIfEqualToLoggedInUser(?ProviderUser $providerUser): void
    {
        // Detect if provider user is attached to the current logged in user, if not throw error
        if ($providerUser === null || $providerUser->getUser() === null || $this->currentUser === null) {
            return;
        }

        if (! $providerUser->getUser()->isEqualTo($this->currentUser)) {
            throw new CurrentUserAlreadyHasAttachedProviderUser();
        }
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
