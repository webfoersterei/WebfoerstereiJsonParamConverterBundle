<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 18.05.21
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class JsonInputDtoValidationException extends NotFoundHttpException
{
    private ConstraintViolationListInterface $constraintViolationList;

    public function __construct(
        ConstraintViolationListInterface $constraintViolationList,
        \Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct('There were validation errors', $previous, $code, $headers);
        $this->constraintViolationList = $constraintViolationList;
    }

    public function generateConstraintViolationDescription(): array
    {
        $errors = [];
        /** @var ConstraintViolationInterface $constraintViolation */
        foreach ($this->constraintViolationList as $constraintViolation) {
            if (!$errors[$constraintViolation->getPropertyPath()]) {
                $errors[$constraintViolation->getPropertyPath()] = [
                    'value'  => $constraintViolation->getInvalidValue(),
                    'errors' => [],
                ];
            }
            $errors[$constraintViolation->getPropertyPath()]['errors'][] = [
                'code' => $constraintViolation->getCode(),
                'msg'  => $constraintViolation->getMessage(),
            ];
        }

        $result = [];
        foreach ($errors as $property => $details) {
            $result[] = array_merge(['property' => $property], $details);
        }

        return $result;
    }
}
