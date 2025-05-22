<?php

namespace App\Core\Serializer;

use Exception;
use RuntimeException;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class DataSerializer
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Data serialization
     *
     * @param object|array|null $data - Data for serialization
     * @param array|string|null $groups - Serialization groups
     * @param string $format - Output format
     * @param array $options - Additional options
     *
     * @return string
     * @throws RuntimeException
     */
    public function serialize(object|array|null $data, array|string|null $groups = null, string $format = "json", array $options = []): string
    {
        if (!is_object($data) && !is_array($data)) {
            throw new RuntimeException("Serialization failed: data must be an object or an array");
        }

        if (is_string($groups)) {
            $groups = [$groups];
        }

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups($groups ?? [])
            ->withCircularReferenceHandler(fn($object) => method_exists($object, "getId") ? $object->getId() : null)
            ->toArray();

        $context = array_merge($options, $context);

        try {
            return $this->serializer->serialize($data, $format, $context);
        }
        catch (Exception $e) {
            throw new RuntimeException("Serialization error: " . $e->getMessage(), 0, $e);
        }
    }
}