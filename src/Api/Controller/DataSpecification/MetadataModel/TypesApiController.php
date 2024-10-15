<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\MetadataModel;

use App\Api\Controller\ApiController;
use App\Api\Resource\DataSpecification\MetadataModel\TypesApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/metadata-model/{model}/v/{version}/types')]
class TypesApiController extends ApiController
{
    #[Route(path: '', name: 'api_metadata_model_types')]
    public function nodes(
        #[MapEntity(mapping: ['model' => 'metadata_model', 'version' => 'id'])]
        MetadataModelVersion $metadataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $metadataModelVersion->getMetadataModel());

        return new JsonResponse((new TypesApiResource())->toArray());
    }
}
