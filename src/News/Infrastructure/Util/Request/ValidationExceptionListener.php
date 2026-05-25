<?php

namespace App\News\Infrastructure\Util\Request;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ValidationExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ValidationFailedException) {
            return;
        }

        $errors = [];
        foreach ($exception->getViolations() as $violation) {
            $errors[] = [
                'property' => $violation->getPropertyPath(),
                'value'    => $violation->getInvalidValue(),
                'message'  => $violation->getMessage(),
            ];
        }

        $event->setResponse(new JsonResponse(
            [
                'ok'      => false,
                'message' => 'validation_failed',
                'errors'  => $errors,
            ],
            Response::HTTP_BAD_REQUEST,
        ));
    }
}