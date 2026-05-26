<?php

namespace App\Identity\Application\EventListener;

use App\Identity\Domain\Event\UserRegistered;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Audit trail for user registration. Emits a structured log line so the event
 * is observable downstream (Monolog → wherever the project ships logs).
 */
#[AsEventListener(event: UserRegistered::class)]
final readonly class LogUserRegistered
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(UserRegistered $event): void
    {
        $this->logger->info('User registered.', [
            'eventId'    => $event->eventId(),
            'occurredOn' => $event->occurredOn()->format(\DateTimeInterface::ATOM),
            'userId'     => $event->userID->value,
            'email'      => $event->email->value,
        ]);
    }
}
