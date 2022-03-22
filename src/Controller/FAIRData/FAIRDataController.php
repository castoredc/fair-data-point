<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Service\UriHelper;
use EasyRdf\RdfNamespace;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use function in_array;

abstract class FAIRDataController extends AbstractController
{
    protected UriHelper $uriHelper;

    protected string $baseUri;

    protected string $basePurl;

    protected LoggerInterface $logger;

    public function __construct(UriHelper $uriHelper, LoggerInterface $logger)
    {
        $this->uriHelper = $uriHelper;
        $this->logger = $logger;

        $this->baseUri = $uriHelper->getBaseUri();
        $this->basePurl = $uriHelper->getBasePurl();

        $this->setNameSpaces();
    }

    protected function acceptsHttp(Request $request): bool
    {
        if ($request->get('format') !== null) {
            return $request->get('format') === 'html';
        }

        return in_array('text/html', $request->getAcceptableContentTypes(), true);
    }

    protected function acceptsTurtle(Request $request): bool
    {
        if ($request->get('format') !== null) {
            return $request->get('format') === 'ttl';
        }

        return in_array('text/turtle', $request->getAcceptableContentTypes(), true) || in_array('text/turtle;q=0.8', $request->getAcceptableContentTypes(), true);
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
        if (! $entity->hasMetadata()) {
            return ['title' => '', 'description' => ''];
        }

        return [
            'title' => $this->getLanguageText($entity->getLatestMetadata()->getTitle(), 'en'),
            'description' => $this->getLanguageText($entity->getLatestMetadata()->getDescription(), 'en'),
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
}
