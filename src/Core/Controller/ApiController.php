<?php

namespace App\Core\Controller;

use App\Core\Serializer\DataSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends AbstractController
{
    private DataSerializer $dataSerializer;

    public function __construct(DataSerializer $dataSerializer)
    {
        $this->dataSerializer = $dataSerializer;
    }

    /**
     * @param string|object|array|null $data
     * @param array $serializationGroups
     * @param array $options
     * @return JsonResponse
     */
    public function getHttpOkResponse(string|object|array|null $data = [], array $serializationGroups = [], array $options = []): JsonResponse
    {
        $serializedData = is_string($data) ? json_encode([
            "message" => $data
        ]) : $this->getSerializedData($data, $serializationGroups, $options);

        return JsonResponse::fromJsonString($serializedData, Response::HTTP_OK);
    }

    /**
     * @param string|object|array|null $data
     * @param array $serializationGroups
     * @param array $options
     * @return JsonResponse
     */
    public function getHttpCreatedResponse(string|object|array|null $data = [], array $serializationGroups = [], array $options = []): JsonResponse
    {
        $serializedData = is_string($data) ? json_encode([
            "message" => $data
        ]) : $this->getSerializedData($data, $serializationGroups, $options);

        return JsonResponse::fromJsonString($serializedData, Response::HTTP_CREATED);
    }

    /**
     * @return JsonResponse
     */
    public function getHttpNoContentResponse(): JsonResponse
    {
        return JsonResponse::fromJsonString("", Response::HTTP_NO_CONTENT);
    }

    /**
     * @param object|array|null $data
     * @param array $serializationGroups
     * @param array $options
     * @return string
     */
    private function getSerializedData(object|array|null $data, array $serializationGroups, array $options): string
    {
        return $this->dataSerializer->serialize($data, $serializationGroups, "json", $options);
    }
}