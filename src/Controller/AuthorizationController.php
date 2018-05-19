<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\CastorAuth;
use App\Service\CastorAuth\RouteParametersStorage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AuthorizationController extends Controller
{

    /**
     * @var CastorAuth
     */
    private $authenticator;

    /**
     * @var RouteParametersStorage
     */
    private $routeParametersStorage;

    /**
     * AuthorizationController constructor.
     * @param CastorAuth $authenticator
     * @param RouteParametersStorage $routeParametersStorage
     */
    public function __construct(CastorAuth $authenticator, RouteParametersStorage $routeParametersStorage)
    {
        $this->authenticator = $authenticator;
        $this->routeParametersStorage = $routeParametersStorage;
    }


    /**
     *  @Route("/auth", name="auth_callback")
     */
    public function oauthCallbackAction(Request $request)
    {
        $code = $request->get('code');
        if (!empty($code)) {
            try {
                $token = $this->authenticator->getAccessTokenByCode($code);
                $routeParameters = $this->routeParametersStorage->getRouteParameters();
                $this->routeParametersStorage->clearRouteParameters();

                return $this->redirectToRoute(
                    $routeParameters->getName(),
                    array_merge(
                        $routeParameters->getParameters(),
                        ['token' => $token]
                    )
                );
            } catch (\Exception $x) {
                return new Response(
                    'Something went wrong during Auth: ' . $x->getMessage()
                );
//                return $this->redirect($this->authenticator->getAuthorizationUrl());
            }
        }
        if (!$this->authenticator->isTokenValid($request->get('token'))) {
            return $this->redirect($this->authenticator->getAuthorizationUrl());
        }

        return true;
    }

}
