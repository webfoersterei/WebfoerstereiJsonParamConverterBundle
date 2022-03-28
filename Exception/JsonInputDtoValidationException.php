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
    private ?object $validatedObject;

    public function __construct(
        ConstraintViolationListInterface $constraintViolationList,
        ?object $validatedObject = null,
        \Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct('There were validation errors', $previous, $code, $headers);
        $this->constraintViolationList = $constraintViolationList;
        $this->validatedObject = $validatedObject;
    }

    public function generateConstraintViolationDescription(): array
    {
        $errors = [];
        /** @var ConstraintViolationInterface $constraintViolation */
        foreach ($this->constraintViolationList as $constraintViolation) {
            if (!isset($errors[$constraintViolation->getPropertyPath()])) {
                $errors[$constraintViolation->getPropertyPath()] = [
                    'property' => $constraintViolation->getPropertyPath(),
                    'value'    => $constraintViolation->getInvalidValue(),
                    'errors'   => [],
                ];
            }
            $errors[$constraintViolation->getPropertyPath()]['errors'][] = [
                'code' => $constraintViolation->getCode(),
                'msg'  => $constraintViolation->getMessage(),
            ];
        }

        return array_values($errors);
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }

    /**
     * @return object|null
     */
    public function getValidatedObject(): ?object
    {
        return $this->validatedObject;
    }
}
