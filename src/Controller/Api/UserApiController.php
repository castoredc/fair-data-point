<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Security\CastorUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserApiController extends ApiController
{
    /**
     * @Route("/api/user", name="api_user")
     */
    public function user(): Response
    {
        /** @var CastorUser|null $user */
        $user = $this->getUser();

        if ($user === null) {
            return new JsonResponse(null);
        }

        return new JsonResponse($user->toArray());
    }
}
