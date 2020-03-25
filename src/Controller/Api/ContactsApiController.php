<?php

namespace App\Controller\Api;

use App\Api\Request\StudyCenterApiRequest;
use App\Api\Request\StudyContactApiRequest;
use App\Exception\GroupedApiRequestParseException;
use App\Message\Api\Study\ClearStudyCentersCommand;
use App\Message\Api\Study\ClearStudyContactsCommand;
use App\Message\Api\Study\CreateDepartmentAndOrganizationCommand;
use App\Message\Api\Study\CreatePersonCommand;
use App\Message\Api\Study\GetStudyCentersCommand;
use App\Message\Api\Study\GetStudyContactsCommand;
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
     * @param string              $studyId
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function getContacts(string $studyId, Request $request, MessageBusInterface $bus): Response
    {
        try {
            $envelope = $bus->dispatch(new GetStudyContactsCommand($studyId));

            $handledStamp = $envelope->last(HandledStamp::class);
            
            return new JsonResponse($handledStamp->getResult()->toArray());
        } catch(HandlerFailedException $e) {
            return new JsonResponse([], 500);
        }
    }

    /**
     * @Route("/api/study/{studyId}/contacts/add", methods={"POST"}, name="api_add_contacts")
     * @param Request             $request
     * @param MessageBusInterface $bus
     *
     * @return Response
     */
    public function addContact(string $studyId, Request $request, MessageBusInterface $bus): Response
    {
        try {
            /** @var StudyContactApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(StudyContactApiRequest::class, $request);

            $envelope = $bus->dispatch(new ClearStudyContactsCommand($studyId));
            $handledStamp = $envelope->last(HandledStamp::class);

            if($handledStamp) {
                foreach ($parsed as $item) {
                    $envelope = $bus->dispatch(
                        new CreatePersonCommand(
                            $studyId,
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