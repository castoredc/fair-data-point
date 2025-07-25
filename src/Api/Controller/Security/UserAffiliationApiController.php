<?php
declare(strict_types=1);

namespace App\Api\Controller\Security;

use App\Api\Controller\ApiController;
use App\Api\Request\Security\UserAffiliationApiRequest;
use App\Command\Agent\CreateAffiliationCommand;
use App\Exception\GroupedApiRequestParseError;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/user')]
class UserAffiliationApiController extends ApiController
{
    #[Route(path: '/affiliations', methods: ['POST'], name: 'api_user_affiliations_update')]
    public function updateAffiliations(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        assert($user instanceof User);

        if ($user->getPerson() === null) {
            throw new AccessDeniedHttpException();
        }

        try {
            /** @var UserAffiliationApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(UserAffiliationApiRequest::class, $request);

            $user->getPerson()->clearAffiliations();

            foreach ($parsed as $item) {
                $this->bus->dispatch(
                    new CreateAffiliationCommand(
                        $user->getPerson(),
                        $item->getOrganizationSource(),
                        $item->getOrganizationCountry(),
                        $item->getDepartmentSource(),
                        $item->getPosition(),
                        $item->getOrganizationId(),
                        $item->getOrganizationName(),
                        $item->getOrganizationCity(),
                        $item->getDepartmentId(),
                        $item->getDepartmentName()
                    )
                );
            }

            return new JsonResponse([]);
        } catch (GroupedApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating the user\'s affiliations', ['exception' => $e]);

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
