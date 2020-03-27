<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\Request\SingleApiRequest;
use App\Exception\ApiRequestParseError;
use App\Exception\GroupedApiRequestParseError;
use App\Model\Castor\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;
use function json_decode;

abstract class ApiController extends AbstractController
{
    /** @var ApiClient */
    protected $apiClient;

    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(ApiClient $apiClient, ValidatorInterface $validator)
    {
        $this->apiClient = $apiClient;
        $this->validator = $validator;
    }

    /**
     * @throws ApiRequestParseError
     */
    protected function parseRequest(string $requestObject, Request $request, bool $multiple = false): SingleApiRequest
    {
        $request = new $requestObject($request);

        $errors = $this->validator->validate($request);

        if ($errors->count() > 0) {
            throw new ApiRequestParseError($errors);
        }

        return $request;
    }

    /**
     * @return array<object>
     *
     * @throws GroupedApiRequestParseError
     */
    protected function parseGroupedRequest(string $requestObject, Request $request): array
    {
        $groupedErrors = [];
        $return = [];

        foreach (json_decode($request->getContent(), true) as $index => $item) {
            $object = new $requestObject($request, $index);
            $errors = $this->validator->validate($object);

            if ($errors->count() > 0) {
                $groupedErrors[$index] = $errors;
            }

            $return[] = $object;
        }

        if (count($groupedErrors) > 0) {
            throw new GroupedApiRequestParseError($groupedErrors);
        }

        return $return;
    }
}
