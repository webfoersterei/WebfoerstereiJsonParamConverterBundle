<?php
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
use Webfoersterei\Bundle\JsonParamConverterBundle\Exception\BadRequestException;

class JsonParamConverter implements ParamConverterInterface
{
    public const VALIDATION_ERRORS_ARGUMENT = 'validationErrorList';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @throws \Webfoersterei\Bundle\JsonParamConverterBundle\Exception\BadRequestException
     * @throws BadRequestException
     */
    public function apply(Request $request, ParamConverter $configuration) {
        if (\strlen($request->getContent()) > 0) {
            if ($request->getContentType() === 'json') {
                try {
                    $className = $configuration->getClass();
                    $object = $this->serializer->deserialize($request->getContent(), $className, 'json');

                    if ($this->validator) {
                        $errors = $this->validator->validate($object);
                        $request->attributes->set(self::VALIDATION_ERRORS_ARGUMENT, $errors);
                    }

                    $request->attributes->set($configuration->getName(), $object);
                } catch (\Exception $ex) {
                    throw new BadRequestException($ex->getMessage(), null, $ex);
                }
            } else {
                throw new BadRequestException();
            }
        }
    }

    /**
     * @param ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration) {
        if (!$configuration->getClass()) {
            return false;
        }

        return true;
    }
}