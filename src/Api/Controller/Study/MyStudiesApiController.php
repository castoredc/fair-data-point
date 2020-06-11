<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Request\Metadata\StudyMetadataFilterApiRequest;
use App\Api\Resource\Metadata\StudyMetadataApiResource;
use App\Api\Resource\PaginatedApiResource;
use App\Api\Resource\Study\StudiesFilterApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Controller\Api\ApiController;
use App\Entity\Study;
use App\Exception\ApiRequestParseError;
use App\Message\Study\FindStudiesByUserCommand;
use App\Message\Study\GetPaginatedStudiesCommand;
use App\Message\Study\GetStudiesCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use function assert;

/**
 * @Route("/api/study")
 */
class MyStudiesApiController extends ApiController
{
    /**
     * @Route("/my", methods={"GET"}, name="api_my_studies")
     */
    public function myStudies(MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var CastorUser $user */
        $user = $this->getUser();
        $envelope = $bus->dispatch(new FindStudiesByUserCommand($user, false));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse($handledStamp->getResult());
    }
}
