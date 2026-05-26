<?php

namespace App\Shared\Infrastructure\Exception;

use App\Shared\Domain\Exception\EntityNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final class EntityNotFoundListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = self::findInChain($event->getThrowable(), EntityNotFoundException::class);
        if ($exception === null) {
            return;
        }

        $event->setResponse(new JsonResponse(
            [
                'ok'      => false,
                'message' => $exception->getMessage(),
            ],
            Response::HTTP_NOT_FOUND,
        ));
    }

    /**
     * Walk the getPrevious() chain looking for an instance of $class. Handles
     * the common case of a domain exception thrown from a Messenger handler
     * being wrapped inside HandlerFailedException.
     */
    private static function findInChain(Throwable $e, string $class): ?Throwable
    {
        for ($current = $e; $current !== null; $current = $current->getPrevious()) {
            if ($current instanceof $class) {
                return $current;
            }
        }
        return null;
    }
}
