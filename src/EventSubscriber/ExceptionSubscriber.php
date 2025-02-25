<?php

namespace App\EventSubscriber;

use App\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationException) {
            $response = new JsonResponse([
                'error' => $exception->getMessage(),
                'violations' => $exception->getErrors(),
            ], JsonResponse::HTTP_BAD_REQUEST);

            $event->setResponse($response);
            return;
        }

        $statusCode = $exception->getCode() > 0 ? $exception->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        $response = new JsonResponse([
            'error' => 'An error occurred',
            'message' => $exception->getMessage(),
        ], $statusCode);

        $event->setResponse($response);
    }
}
