<?php
declare(strict_types=1);

namespace App\Api\Controller\DataSpecification\DataModel;

use App\Api\Controller\ApiController;
use App\Api\Resource\DataSpecification\DataModel\TypesApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/data-model/{model}/v/{version}/types')]
#[ParamConverter('dataModelVersion', options: ['mapping' => ['model' => 'data_model', 'version' => 'id']])]
class TypesApiController extends ApiController
{
    #[Route(path: '', name: 'api_data_model_types')]
    public function nodes(DataModelVersion $dataModelVersion): Response
    {
        $this->denyAccessUnlessGranted('view', $dataModelVersion->getDataModel());

        return new JsonResponse((new TypesApiResource())->toArray());
    }
}
