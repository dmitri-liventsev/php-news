<?php

namespace App\Shared\Domain\Exception;

use DomainException;

/**
 * Base class for domain rule violations that map to HTTP 409 Conflict
 * (e.g. unique-key collisions, optimistic-lock failures, attempt to act on
 * an entity in the wrong state). Concrete subclasses live in each bounded
 * context (EmailAlreadyTakenException, …).
 */
abstract class DomainConflictException extends DomainException
{
}
