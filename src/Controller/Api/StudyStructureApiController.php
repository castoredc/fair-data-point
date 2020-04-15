<?php

namespace App\Controller\Api;

use App\Api\Resource\Dataset\DatasetApiResource;
use App\Api\Resource\StudyStructure\StudyStructureApiResource;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Message\Country\GetCountriesCommand;
use App\Message\Study\GetStudyStructureCommand;
use App\Security\CastorUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class StudyStructureApiController extends ApiController
{
    /**
     * @Route("/api/study/{study}/structure", name="api_study_structure")
     * @ParamConverter("study", options={"mapping": {"study": "slug"}})
     */
    public function studyStructure(Study $study, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $study);

        /** @var CastorUser|null $user */
        $user = $this->getUser();

        $envelope = $bus->dispatch(new GetStudyStructureCommand($study, $user));

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);

        return new JsonResponse((new StudyStructureApiResource($handledStamp->getResult()))->toArray());
    }
}