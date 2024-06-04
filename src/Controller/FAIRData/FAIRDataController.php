<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Command\Metadata\RenderRDFMetadataCommand;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Service\UriHelper;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use function assert;
use function dump;
use function in_array;
use function time;

abstract class FAIRDataController extends AbstractController
{
    protected string $baseUri;

    protected string $basePurl;

    public function __construct(protected UriHelper $uriHelper, protected LoggerInterface $logger)
    {
        $this->baseUri = $uriHelper->getBaseUri();
        $this->basePurl = $uriHelper->getBasePurl();

        $this->setNameSpaces();
    }

    protected function acceptsFormat(Request $request, string $format, string $mimeType): bool
    {
        if ($request->get('format') !== null) {
            return $request->get('format') === $format;
        }

        return in_array($mimeType, $request->getAcceptableContentTypes(), true);
    }

    protected function acceptsHttp(Request $request): bool
    {
        return $this->acceptsFormat($request, 'html', 'text/html');
    }

    protected function setNameSpaces(): void
    {
        RdfNamespace::set('r3d', 'http://www.re3data.org/schema/3-0#');
        RdfNamespace::set('fdp', 'http://rdf.biosemantics.org/ontologies/fdp-o#');
        RdfNamespace::set('ldp', 'http://www.w3.org/ns/ldp#');
        RdfNamespace::set('datacite', 'http://purl.org/spar/datacite/');
        RdfNamespace::set('ejprd', 'http://purl.org/ejp-rd/vocabulary/');
    }

    /** @return mixed[] */
    protected function getSeoTexts(MetadataEnrichedEntity $entity): array
    {
        return [
            'title' => $entity->hasMetadata() && $entity->getLatestMetadata()->getTitle() !== null ? $this->getLanguageText(
                $entity->getLatestMetadata()->getTitle(),
                'en'
            ) : '',
            'description' => $entity->hasMetadata() && $entity->getLatestMetadata()->getDescription() !== null ? $this->getLanguageText(
                $entity->getLatestMetadata()->getDescription(),
                'en'
            ) : '',
        ];
    }

    /** @return mixed[] */
    protected function getAgentSeoTexts(Agent $agent): array
    {
        return [
            'title' => $agent->getName(),
        ];
    }

    private function getLanguageText(LocalizedText $localizedText, string $language): string
    {
        if (! $localizedText->hasTexts()) {
            return '';
        }

        $item = $localizedText->getTextByLanguageString($language) ?? $localizedText->getTexts()->first();

        return $item->getText();
    }

    protected function getTurtleResponse(MetadataEnrichedEntity $entity, string $turtle, bool $download): Response
    {
        if ($download === true) {
            $response = new Response($turtle);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $entity->getSlug() . '_' . time() . '.ttl'
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

    protected function renderResource(
        Request $request,
        MetadataEnrichedEntity $entity,
        MessageBusInterface $bus,
    ): mixed {
        if ($this->acceptsHttp($request)) {
            return $this->render('react.html.twig');
        }

        try {
            $handledStamp = $bus->dispatch(new RenderRDFMetadataCommand($entity))->last(
                HandledStamp::class
            );

            assert($handledStamp instanceof HandledStamp);

            $graph = $handledStamp->getResult();
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while loading the RDF', [
                'exception' => $e,
                'Distribution' => $entity->getId(),
            ]);

            dump($e);

            return new JsonResponse(['error' => 'An error occurred while loading the RDF.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        assert($graph instanceof Graph);

        if ($this->acceptsFormat($request, 'json', 'application/json')) {
            return new Response(
                $graph->serialise('json'),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }

        if ($this->acceptsFormat($request, 'jsonld', 'application/ld+json')) {
            return new Response(
                $graph->serialise('jsonld'),
                Response::HTTP_OK,
                ['content-type' => 'application/ld+json']
            );
        }

        return new Response(
            $graph->serialise('turtle'),
            Response::HTTP_OK,
            ['content-type' => 'text/turtle']
        );
    }

    protected function renderRdf(
        MetadataEnrichedEntity $entity,
        MessageBusInterface $bus,
        bool $shouldDownload,
    ): mixed {
        try {
            $handledStamp = $bus->dispatch(new RenderRDFMetadataCommand($entity))->last(
                HandledStamp::class
            );
            assert($handledStamp instanceof HandledStamp);

            $graph = $handledStamp->getResult();
            assert($graph instanceof Graph);
            $turtle = $graph->serialise('turtle');
        } catch (HandlerFailedException $e) {
            $e = $e->getPrevious();

            $this->logger->critical('An error occurred while loading the RDF', [
                'exception' => $e,
                'Distribution' => $entity->getId(),
            ]);

            dump($e);

            return new JsonResponse(['error' => 'An error occurred while loading the RDF.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getTurtleResponse($entity, $turtle, $shouldDownload);
    }
}
