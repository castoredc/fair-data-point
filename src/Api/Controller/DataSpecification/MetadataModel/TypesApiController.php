<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Resource\DataSpecification\MetadataModel\TypesApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/metadata-model/{model}/v/{version}/types")
 * @ParamConverter("metadataModelVersion", options={"mapping": {"model": "metadata_model", "version": "id"}})
 */
class TypesApiController extends ApiController
{
    /** @Route("", name="api_metadata_model_types") */
    public function nodes(MetadataModelVersion $metadataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new TypesApiResource())->toArray());
    }
}
