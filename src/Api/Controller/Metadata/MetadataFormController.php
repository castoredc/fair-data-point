<?php
declare(strict_types=1);

namespace App\Api\Controller\Metadata;

use App\Api\Controller\ApiController;
use App\Api\Resource\Metadata\MetadataFormsApiResource;
use App\Entity\Metadata\Metadata;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/metadata/form/{metadata}")
 * @ParamConverter("metadata", options={"mapping": {"metadata": "id"}})
 */
class MetadataFormController extends ApiController
{
    /** @Route("", methods={"GET"}, name="api_metadata_metadata_form_get") */
    public function getMetadataForm(Metadata $metadata, Request $request, MessageBusInterface $bus): Response
    {
        $this->denyAccessUnlessGranted('edit', $metadata->getEntity());

        return new JsonResponse((new MetadataFormsApiResource($metadata))->toArray());
    }
}
