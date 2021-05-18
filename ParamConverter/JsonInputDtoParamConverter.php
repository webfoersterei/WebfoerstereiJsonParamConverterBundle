<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 06.03.18
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonInputDtoParamConverter implements ParamConverterInterface
{
    private static bool $wasExecuted = false;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public static function wasExecuted(): bool
    {
        return self::$wasExecuted;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        if ($request->getContent() && 'json' === $request->getContentType()) {
            $className = $configuration->getClass();
            $object = $this->serializer->deserialize($request->getContent(), $className, 'json');

            $errors = $this->validator->validate($object);
            $request->attributes->set(ConstraintErrorListParamConverter::VALIDATION_ERRORS_ARGUMENT, $errors);

            $request->attributes->set($configuration->getName(), $object);

            self::$wasExecuted = true;

            return true;
        }

        return false;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function supports(ParamConverter $configuration)
    {
        if (!$configuration->getClass()) {
            return false;
        }

        $reflection = new \ReflectionClass($configuration->getClass());
        if (!$reflection->getAttributes(JsonInputDto::class)) {
            return false;
        }

        return true;
    }
}
