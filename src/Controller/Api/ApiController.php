<?php

namespace App\Controller\Api;

use App\Api\Request\SingleApiRequest;
use App\Exception\ApiRequestParseException;
use App\Exception\GroupedApiRequestParseException;
use App\Model\Castor\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ApiController extends AbstractController
{
    /** @var ApiClient */
    protected $apiClient;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ApiClient $apiClient, ValidatorInterface $validator)
    {
        $this->apiClient = $apiClient;
        $this->validator = $validator;
    }
    /**
     * @throws ApiRequestParseException
     */
    protected function parseRequest(string $requestObject, Request $request, bool $multiple = false): SingleApiRequest
    {
        $request = new $requestObject($request);

        $errors = $this->validator->validate($request);

        if ($errors->count() > 0) {
            throw new ApiRequestParseException($errors);
        }

        return $request;
    }

    /**
     * @throws GroupedApiRequestParseException
     */
    protected function parseGroupedRequest(string $requestObject, Request $request): array
    {
        $groupedErrors = [];
        $return = [];

        foreach(json_decode($request->getContent(), true) as $index => $item)
        {
            $object = new $requestObject($request, $index);
            $errors = $this->validator->validate($object);

            if ($errors->count() > 0) {
                $groupedErrors[$index] = $errors;
            }

            $return[] = $object;
        }

        if (count($groupedErrors) > 0) {
            throw new GroupedApiRequestParseException($groupedErrors);
        }

        return $return;
    }
}