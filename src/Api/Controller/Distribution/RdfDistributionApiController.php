<?php
declare(strict_types=1);

namespace App\Api\Controller\Distribution;

use App\Api\Controller\ApiController;
use App\Api\Request\Distribution\DataModelMappingApiRequest;
use App\Api\Resource\Distribution\DataModelMappingApiResource;
use App\Command\Distribution\RDF\CreateDataModelModuleMappingCommand;
use App\Command\Distribution\RDF\CreateDataModelNodeMappingCommand;
use App\Command\Distribution\RDF\GetDataModelMappingCommand;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\Common\Mapping\Mapping;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\Enum\DataModelMappingType;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\PaginatedResultCollection;
use App\Exception\ApiRequestParseError;
use App\Exception\DataSpecification\Common\Model\MappingAlreadyExists;
use App\Exception\Distribution\RDF\InvalidSyntax;
use App\Exception\Distribution\RDF\VariableNotSelected;
use App\Exception\InvalidDistributionType;
use App\Exception\InvalidEntityType;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Security\Authorization\Voter\DistributionVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

#[Route(path: '/api/dataset/{dataset}/distribution/{distribution}/contents/rdf/v/{version}/{type}')]
class RdfDistributionApiController extends ApiController
{
    #[Route(path: '', methods: ['GET'], name: 'api_distribution_contents_rdf')]
    public function distributionRdfContents(
        string $type,
        #[MapEntity(mapping: ['version' => 'id'])]
        DataModelVersion $dataModelVersion,
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
        #[MapEntity(mapping: ['distribution' => 'slug'])]
        Distribution $distribution,
    ): Response {
        $this->denyAccessUnlessGranted(DistributionVoter::EDIT, $distribution);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        $contents = $distribution->getContents();

        if (! $contents instanceof RDFDistribution) {
            throw new InvalidDistributionType();
        }

        $type = DataModelMappingType::fromString($type);

        $envelope = $this->bus->dispatch(new GetDataModelMappingCommand($contents, $dataModelVersion, $type));

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        $results = $handledStamp->getResult();
        assert($results instanceof PaginatedResultCollection);

        return $this->getPaginatedResponse(DataModelMappingApiResource::class, $results);
    }

    #[Route(path: '', methods: ['POST'], name: 'api_distribution_contents_rdf_add')]
    public function addMapping(
        string $type,
        #[MapEntity(mapping: ['version' => 'id'])]
        DataModelVersion $dataModelVersion,
        #[MapEntity(mapping: ['dataset' => 'slug'])]
        Dataset $dataset,
        #[MapEntity(mapping: ['distribution' => 'slug'])]
        Distribution $distribution,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted(DistributionVoter::EDIT, $distribution);

        if (! $dataset->hasDistribution($distribution)) {
            throw $this->createNotFoundException();
        }

        $contents = $distribution->getContents();

        if (! $contents instanceof RDFDistribution) {
            throw new InvalidDistributionType();
        }

        $type = DataModelMappingType::fromString($type);

        try {
            $parsed = $this->parseRequest(DataModelMappingApiRequest::class, $request);
            assert($parsed instanceof DataModelMappingApiRequest);

            if ($type->isNode()) {
                $envelope = $this->bus->dispatch(
                    new CreateDataModelNodeMappingCommand(
                        $contents,
                        $parsed->getNode(),
                        $parsed->getElements(),
                        $parsed->getTransform(),
                        $parsed->getTransformSyntax(),
                        $dataModelVersion
                    )
                );
            } elseif ($type->isModule()) {
                $envelope = $this->bus->dispatch(
                    new CreateDataModelModuleMappingCommand(
                        $contents,
                        $parsed->getModule(),
                        $parsed->getElement(),
                        $parsed->getStructureType(),
                        $dataModelVersion
                    )
                );
            } else {
                throw new InvalidEntityType();
            }

            $handledStamp = $envelope->last(HandledStamp::class);
            assert($handledStamp instanceof HandledStamp);

            $result = $handledStamp->getResult();
            assert($result instanceof Mapping);

            return new JsonResponse((new DataModelMappingApiResource($result))->toArray());
        } catch (ApiRequestParseError $e) {
            return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            if ($e instanceof NoAccessPermission) {
                return new JsonResponse($e->toArray(), Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof MappingAlreadyExists) {
                return new JsonResponse($e->toArray(), Response::HTTP_CONFLICT);
            }

            if ($e instanceof NotFound) {
                return new JsonResponse($e->toArray(), Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof InvalidSyntax) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            if ($e instanceof VariableNotSelected) {
                return new JsonResponse($e->toArray(), Response::HTTP_BAD_REQUEST);
            }

            $this->logger->critical(
                'An error occurred while adding a mapping to an RDF distribution',
                [
                    'exception' => $e,
                    'Distribution' => $distribution->getSlug(),
                    'DistributionID' => $distribution->getId(),
                ]
            );

            return new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
