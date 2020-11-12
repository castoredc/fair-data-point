<?php
declare(strict_types=1);

namespace App\Api\Controller\Security;

use App\Api\Controller\ApiController;
use App\Api\Request\Security\UserAffiliationApiRequest;
use App\Exception\GroupedApiRequestParseError;
use App\Command\Agent\CreateAffiliationCommand;
use App\Security\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

/**
 * @Route("/api/user")
 */
class UserAffiliationApiController extends ApiController
{
    /**
     * @Route("/affiliations", methods={"POST"}, name="api_user_affiliations_update")
     */
    public function updateAffiliations(Request $request, MessageBusInterface $bus): Response
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
                $bus->dispatch(new CreateAffiliationCommand(
                    $user->getPerson(),
                    $item->getOrganizationSource(),
                    $item->getOrganizationId(),
                    $item->getOrganizationName(),
                    $item->getOrganizationCity(),
                    $item->getOrganizationCountry(),
                    $item->getDepartmentSource(),
                    $item->getDepartmentId(),
                    $item->getDepartmentName(),
                    $item->getPosition()
                ));
            }

            return new JsonResponse([], 200);
        } catch (GroupedApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        } catch (HandlerFailedException $e) {
            $this->logger->critical('An error occurred while updating the user\'s affiliations', ['exception' => $e]);

            return new JsonResponse([], 500);
        }
    }
}
