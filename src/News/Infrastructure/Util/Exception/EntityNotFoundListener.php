<?php

namespace App\News\Infrastructure\Util\Exception;

use App\News\Domain\Exception\EntityNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class EntityNotFoundListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof EntityNotFoundException) {
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
}
