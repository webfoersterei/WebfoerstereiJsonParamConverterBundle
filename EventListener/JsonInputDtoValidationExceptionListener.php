<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 18.05.21
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Webfoersterei\Bundle\JsonParamConverterBundle\Exception\JsonInputDtoValidationException;

class JsonInputDtoValidationExceptionListener
{
    /**
     * @var int|null
     */
    private ?int $httpResponseCode;

    public function __construct(?int $httpResponseCode = null)
    {
        $this->httpResponseCode = $httpResponseCode;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // Should we convert a inputDtoValidationException into a httpResponse?
        if (!$this->httpResponseCode) {
            return;
        }

        // You get the exception object from the received event
        $exception = $event->getThrowable();

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof JsonInputDtoValidationException) {
            $event->setResponse(
                new JsonResponse(
                    $exception->generateConstraintViolationDescription(),
                    Response::HTTP_BAD_REQUEST
                )
            );
        }
    }
}
