<?php
declare(strict_types=1);

namespace App\Api\Controller\FAIRData;

use App\Api\Controller\ApiController;
use App\Api\Resource\License\LicenseApiResource;
use App\Command\License\GetLicensesCommand;
use App\Entity\FAIRData\License;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use function assert;

class LicensesApiController extends ApiController
{
    /** @Route("/api/licenses", name="api_licenses") */
    public function countries(MessageBusInterface $bus): Response
    {
        $envelope = $bus->dispatch(new GetLicensesCommand());

        $handledStamp = $envelope->last(HandledStamp::class);
        assert($handledStamp instanceof HandledStamp);

        return new JsonResponse($handledStamp->getResult()->toArray());
    }

    /**
     * @Route("/api/license/{slug}", name="api_license")
     * @ParamConverter("license", options={"mapping": {"slug": "slug"}})
     */
    public function license(License $license): Response
    {
        return new JsonResponse((new LicenseApiResource($license))->toArray());
    }
}
