<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Resource\DataSpecification\DataModel\TypesApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/data-model/{model}/v/{version}/types')]
class TypesApiController extends ApiController
{
    #[Route(path: '', name: 'api_data_model_types')]
    public function nodes(
        #[MapEntity(mapping: ['model' => 'dataSpecification', 'version' => 'id'])]
        DataModelVersion $dataModelVersion,
    ): Response {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new TypesApiResource())->toArray());
    }
}
