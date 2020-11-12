<?php
declare(strict_types=1);

namespace App\Api\Controller\Study;

use App\Api\Controller\ApiController;
use App\Api\Request\Study\Provenance\StudyContactApiRequest;
use App\Command\Agent\AddStudyContactCommand;
use App\Command\Study\Provenance\ClearStudyContactsCommand;
use App\Command\Study\Provenance\GetStudyContactsCommand;
use App\Entity\Study;
use App\Exception\GroupedApiRequestParseError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class StudyContactsApiController extends ApiController
{
    /**
     * @Route("/api/study/{studyId}/contacts", methods={"GET"}, name="api_get_contacts")
     * @ParamConverter("study", options={"mapping": {"studyId": "id"}})
     */
    public function getContacts(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('view', $study);

        try {
            $envelope = $bus->dispatch(new GetStudyContactsCommand($study));

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

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
        $this->denyAccessUnlessGranted('edit', $study);

        try {
            /** @var StudyContactApiRequest[] $parsed */
            $parsed = $this->parseGroupedRequest(StudyContactApiRequest::class, $request);

            $bus->dispatch(new ClearStudyContactsCommand($study));

            foreach ($parsed as $item) {
                $bus->dispatch(
                    new AddStudyContactCommand(
                        $study,
                        $item->getId(),
                        $item->getFirstName(),
                        $item->getMiddleName(),
                        $item->getLastName(),
                        $item->getEmail(),
                        $item->getOrcid()
                    )
                );
            }

            return new JsonResponse([], 200);
        } catch (GroupedApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), 400);
        }
    }
}
