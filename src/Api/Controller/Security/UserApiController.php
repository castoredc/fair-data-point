<?php
declare(strict_types=1);

namespace App\Api\Controller\Security;

use App\Api\Controller\ApiController;
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
