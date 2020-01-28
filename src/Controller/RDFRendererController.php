<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\FAIRData\Distribution\RDFDistribution\RDFDistribution;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\Organization;
use App\Entity\FAIRData\Person;
use App\Helper\RDFTwigRenderHelper;
use App\Model\Castor\ApiClient;
use App\Security\CastorUser;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use function in_array;
use function time;

class RDFRendererController extends AbstractController
{
    public const ACCEPT_HTTP = 1;
    public const ACCEPT_JSON = 2;
    public const ACCEPT_TURTLE = 3;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    private function detectAccept(Request $request): int
    {
        if ($request->get('format') !== null) {
            $format = $request->get('format');

            if ($format === 'html') {
                return self::ACCEPT_HTTP;
            }

            if ($format === 'json') {
                return self::ACCEPT_JSON;
            }

            if ($format === 'ttl') {
                return self::ACCEPT_TURTLE;
            }
        }

        $types = $request->getAcceptableContentTypes();

        if (in_array('text/html', $types, true)) {
            return self::ACCEPT_HTTP;
        }

        if (in_array('application/json', $types, true)) {
            return self::ACCEPT_JSON;
        }

        if (in_array('text/turtle', $types, true)) {
            return self::ACCEPT_TURTLE;
        }

        if (in_array('text/turtle;q=0.8', $types, true)) {
            return self::ACCEPT_TURTLE;
        }

        return self::ACCEPT_TURTLE;
    }

    /**
     * @Route("/fdp", name="fdp_render")
     */
    public function fdpAction(Request $request): Response
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);

        /** @var FAIRDataPoint|null $fdp */
        $fdp = $fairDataPointRepository->findOneBy(['iri' => $uri]);

        if ($fdp === null) {
            throw new NotFoundHttpException('FAIR Data Point not found');
        }

        if ($accept === self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }

        if ($accept === self::ACCEPT_JSON) {
            return new JsonResponse(
                [
                    'success' => true,
                    'fdp' => $fdp->toArray(),
                ]
            );
        }

        $graph = $fdp->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @Route("/agent/{agentType}/{agentSlug}", name="agent_render")
     */
    public function profileAction(Request $request, string $agentType, string $agentSlug): Response
    {
        $accept = $this->detectAccept($request);

        $doctrine = $this->getDoctrine();
        $personRepository = $doctrine->getRepository(Person::class);
        $organizationRepository = $doctrine->getRepository(Organization::class);

        /** @var Agent $contact */
        $contact = null;
        if ($agentType === 'person') {
            $contact = $personRepository->findOneBy(['slug' => $agentSlug]);
        }
        if ($agentType === 'organization') {
            $contact = $organizationRepository->findOneBy(['slug' => $agentSlug]);
        }

        if ($contact === null) {
            throw new NotFoundHttpException('Agent not found');
        }

        if ($accept === self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }

        if ($accept === self::ACCEPT_JSON) {
            return new JsonResponse(
                [
                    'success' => true,
                    'agent' => $contact->toArray(),
                ]
            );
        }

        $graph = $contact->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}", name="catalog_render")
     */
    public function catalogAction(Request $request, string $catalogSlug): Response
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);

        /** @var FAIRDataPoint|null $fdp */
        $fdp = $fairDataPointRepository->findOneBy(['iri' => $uri]);
        if ($fdp === null) {
            throw new NotFoundHttpException('FAIR Data Point not found');
        }

        /** @var Catalog|null $catalog */
        $catalog = $catalogRepository->findOneBy(['slug' => $catalogSlug, 'fairDataPoint' => $fdp]);
        if ($catalog === null) {
            throw new NotFoundHttpException('Catalog not found');
        }

        if ($accept === self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }

        if ($accept === self::ACCEPT_JSON) {
            $response = [
                'success' => true,
                'catalog' => $catalog->toArray(),
            ];

            if ($request->get('ui') !== null) {
                $response['fdp'] = $fdp->toBasicArray();
            }

            return new JsonResponse($response);
        }
        $graph = $catalog->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}", name="dataset_render")
     */
    public function datasetAction(Request $request, string $catalogSlug, string $datasetSlug): Response
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);

        /** @var FAIRDataPoint|null $fdp */
        $fdp = $fairDataPointRepository->findOneBy(['iri' => $uri]);
        if ($fdp === null) {
            throw new NotFoundHttpException('FAIR Data Point not found');
        }

        /** @var Catalog|null $catalog */
        $catalog = $catalogRepository->findOneBy(['slug' => $catalogSlug, 'fairDataPoint' => $fdp]);
        if ($catalog === null) {
            throw new NotFoundHttpException('Catalog not found');
        }

        /** @var Dataset|null $dataset */
        $dataset = $datasetRepository->findOneBy(['slug' => $datasetSlug]);
        if ($dataset === null || ! $dataset->hasCatalog($catalog)) {
            throw new NotFoundHttpException('Dataset not found');
        }

        if ($accept === self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }

        if ($accept === self::ACCEPT_JSON) {
            $response = [
                'success' => true,
                'dataset' => $dataset->toArray(),
            ];

            if ($request->get('ui') !== null) {
                $response['catalog'] = $catalog->toBasicArray();
            }

            return new JsonResponse($response);
        }
        $graph = $dataset->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/{distributionSlug}", name="distribution_render")
     */
    public function distributionAction(Request $request, string $catalogSlug, string $datasetSlug, string $distributionSlug): Response
    {
        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);
        $distributionRepository = $doctrine->getRepository(Distribution::class);

        /** @var FAIRDataPoint|null $fdp */
        $fdp = $fairDataPointRepository->findOneBy(['iri' => $uri]);
        if ($fdp === null) {
            throw new NotFoundHttpException('FAIR Data Point not found');
        }

        /** @var Catalog|null $catalog */
        $catalog = $catalogRepository->findOneBy(['slug' => $catalogSlug, 'fairDataPoint' => $fdp]);
        if ($catalog === null) {
            throw new NotFoundHttpException('Catalog not found');
        }

        /** @var Dataset|null $dataset */
        $dataset = $datasetRepository->findOneBy(['slug' => $datasetSlug]);
        if ($dataset === null || ! $dataset->hasCatalog($catalog)) {
            throw new NotFoundHttpException('Dataset not found');
        }

        /** @var Distribution|null $distribution */
        $distribution = $distributionRepository->findOneBy(['slug' => $distributionSlug, 'dataset' => $dataset]);
        if ($distribution === null) {
            throw new NotFoundHttpException('Distribution not found');
        }

        if ($accept === self::ACCEPT_HTTP) {
            return $this->render(
                'react.html.twig'
            );
        }

        if ($accept === self::ACCEPT_JSON) {
            $response = [
                'success' => true,
                'distribution' => $distribution->toArray(),
            ];

            if ($request->get('ui') !== null) {
                $response['dataset'] = $dataset->toBasicArray();
            }

            return new JsonResponse($response);
        }

        $graph = $distribution->toGraph();

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @throws Exception
     *
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/{distributionSlug}/rdf", name="rdf_render")
     */
    public function rdfAction(Request $request, string $catalogSlug, string $datasetSlug, string $distributionSlug): Response
    {
        /** @var CastorUser|null $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->redirect('/connect/castor?target_path=' . $request->getRequestUri());
        }

        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);
        $distributionRepository = $doctrine->getRepository(Distribution::class);

        /** @var FAIRDataPoint|null $fdp */
        $fdp = $fairDataPointRepository->findOneBy(['iri' => $uri]);
        if ($fdp === null) {
            throw new NotFoundHttpException('FAIR Data Point not found');
        }

        /** @var Catalog|null $catalog */
        $catalog = $catalogRepository->findOneBy(['slug' => $catalogSlug, 'fairDataPoint' => $fdp]);
        if ($catalog === null) {
            throw new NotFoundHttpException('Catalog not found');
        }

        /** @var Dataset|null $dataset */
        $dataset = $datasetRepository->findOneBy(['slug' => $datasetSlug]);
        if ($dataset === null || ! $dataset->hasCatalog($catalog)) {
            throw new NotFoundHttpException('Dataset not found');
        }

        $this->apiClient->setToken($user->getToken());

        $distribution = $distributionRepository->findOneBy(['slug' => $distributionSlug, 'dataset' => $dataset]);
        if ($distribution === null) {
            throw new NotFoundHttpException('Distribution not found');
        }
        if (! ($distribution instanceof RDFDistribution)) {
            throw new NotFoundHttpException('This distribution is not a RDF distribution');
        }

        try {
            $study = $this->apiClient->getStudy($dataset->getStudy()->getId());
        } catch (UnauthorizedHttpException $e) {
            throw new UnauthorizedHttpException('', 'You do not have permission to access this study');
        }

        /** @var Environment $twigEnvironment */
        $twigEnvironment = $this->get('twig');
        $helper = new RDFTwigRenderHelper($this->apiClient, $study, $twigEnvironment, $distribution);

        $turtle = $distribution->getPrefix() . "\n\n" . $helper->renderRecords();

        if ($request->query->getBoolean('download') === true) {
            $response = new Response($turtle);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $study->getSlug() . '_' . time() . '.ttl'
            );
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return new Response(
            $turtle,
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    /**
     * @throws Exception
     *
     * @Route("/fdp/{catalogSlug}/{datasetSlug}/{distributionSlug}/rdf/{recordId}", name="rdf_render_record")
     */
    public function rdfRecordAction(Request $request, string $catalogSlug, string $datasetSlug, string $distributionSlug, string $recordId): Response
    {
        /** @var CastorUser|null $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->redirect('/connect/castor?target_path=' . $request->getRequestUri());
        }

        $accept = $this->detectAccept($request);
        $uri = $request->getSchemeAndHttpHost();

        $doctrine = $this->getDoctrine();
        $fairDataPointRepository = $doctrine->getRepository(FAIRDataPoint::class);
        $catalogRepository = $doctrine->getRepository(Catalog::class);
        $datasetRepository = $doctrine->getRepository(Dataset::class);
        $distributionRepository = $doctrine->getRepository(Distribution::class);

        /** @var FAIRDataPoint|null $fdp */
        $fdp = $fairDataPointRepository->findOneBy(['iri' => $uri]);
        if ($fdp === null) {
            throw new NotFoundHttpException('FAIR Data Point not found');
        }

        /** @var Catalog|null $catalog */
        $catalog = $catalogRepository->findOneBy(['slug' => $catalogSlug, 'fairDataPoint' => $fdp]);
        if ($catalog === null) {
            throw new NotFoundHttpException('Catalog not found');
        }

        /** @var Dataset|null $dataset */
        $dataset = $datasetRepository->findOneBy(['slug' => $datasetSlug]);
        if ($dataset === null || ! $dataset->hasCatalog($catalog)) {
            throw new NotFoundHttpException('Dataset not found');
        }

        $this->apiClient->setToken($user->getToken());

        $distribution = $distributionRepository->findOneBy(['slug' => $distributionSlug, 'dataset' => $dataset]);

        if ($distribution === null) {
            throw new NotFoundHttpException('Distribution not found');
        }
        if (! ($distribution instanceof RDFDistribution)) {
            throw new NotFoundHttpException('This distribution is not a RDF distribution');
        }

        try {
            $study = $this->apiClient->getStudy($dataset->getStudy()->getId());
        } catch (UnauthorizedHttpException $e) {
            throw new UnauthorizedHttpException('', 'You do not have permission to access this study');
        }

        try {
            $record = $this->apiClient->getRecord($study, $recordId);
        } catch (NotFoundHttpException $e) {
            throw new NotFoundHttpException('Record not found');
        }

        $helper = new RDFTwigRenderHelper($this->apiClient, $study, $this->get('twig'), $distribution);

        $turtle = $distribution->getPrefix() . "\n\n" . $helper->renderRecord($record);

        if ($request->query->getBoolean('download') === true) {
            $response = new Response($turtle);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $study->getSlug() . '_' . $recordId . '_' . time() . '.ttl'
            );
            $response->headers->set('Content-Disposition', $disposition);

            return $response;
        }

        return new Response(
            $turtle,
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }
}
