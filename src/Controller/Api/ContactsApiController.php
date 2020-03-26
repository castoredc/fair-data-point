<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\StudyContactApiRequest;
use App\Entity\Castor\Study;
use App\Exception\GroupedApiRequestParseException;
use App\Message\Api\Study\ClearStudyContactsCommand;
use App\Message\Api\Study\CreatePersonCommand;
use App\Message\Api\Study\GetStudyContactsCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class ContactsApiController extends ApiController
{
    /**
     * @Route("/api/study/{studyId}/contacts", methods={"GET"}, name="api_get_contacts")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     * @param Study               $studyId
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function getContacts(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new GetStudyContactsCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);

            return new JsonResponse($handledStamp->getResult()->toArray());
        } catch (HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/contacts/add", methods={"POST"}, name="api_add_contacts")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function addContact(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var StudyContactApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(StudyContactApiRequest::class, $request);

            $envelope = $bus->dispatch(new ClearStudyContactsCommand($study));
            $handledStamp = $envelope->last(HandledStamp::class);

            if ($handledStamp) {
                foreach ($parsed as $item) {
                    $envelope = $bus->dispatch(
                        new CreatePersonCommand(
                            $study,
                            $item->getFirstName(),
                            $item->getMiddleName(),
                            $item->getLastName(),
                            $item->getEmail(),
                            $item->getOrcid()
                        )
                    );

                    $handledStamp = $envelope->last(HandledStamp::class);
                }

                return new JsonResponse([], 200);
            }
        } catch (GroupedApiRequestParseException $e) {
            return new JsonResponse($e->toArray(), 400);
        }
        // catch(HandlerFailedException $e) {
        //     return new JsonResponse([], 500);
        // }
    }
}
