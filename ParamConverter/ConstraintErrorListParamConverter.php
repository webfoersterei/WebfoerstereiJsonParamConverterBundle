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
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintErrorListParamConverter implements ParamConverterInterface
{
    public const VALIDATION_ERRORS_ARGUMENT = 'validationErrorList';

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $errors = $request->attributes->get(self::VALIDATION_ERRORS_ARGUMENT);

        if (null !== $errors) {
            $request->attributes->set($configuration->getName(), $errors);

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
        return $configuration->getClass() === ConstraintViolationListInterface::class
               && JsonInputDtoParamConverter::wasExecuted();
    }
}
