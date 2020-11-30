<?php
declare(strict_types=1);

namespace App\Controller\FAIRData;

use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Service\UriHelper;
use EasyRdf\RdfNamespace;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use function in_array;

abstract class FAIRDataController extends AbstractController
{
    protected UriHelper $uriHelper;

    protected string $baseUri;

    public function __construct(UriHelper $uriHelper)
    {
        $this->uriHelper = $uriHelper;
        $this->baseUri = $uriHelper->getBaseUri();

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
        RdfNamespace::set('datacite', 'http://purl.org/spar/datacite/');
    }

    /** @return mixed[] */
    protected function getSeoTexts(MetadataEnrichedEntity $entity): array
    {
        if (! $entity->hasMetadata()) {
            return ['title' => '', 'description' => ''];
        }

        return [
            'title' => $entity->getLatestMetadata()->getTitle()->getTextByLanguageString('en')->getText(),
            'description' => $entity->getLatestMetadata()->getDescription()->getTextByLanguageString('en')->getText(),
        ];
    }
}
