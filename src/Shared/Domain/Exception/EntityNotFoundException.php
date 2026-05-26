<?php

namespace App\Shared\Domain\Exception;

use DomainException;

/**
 * Base class for "entity with this id was not found" errors.
 *
 * Concrete subclasses live in each bounded context (ArticleNotFoundException,
 * UserNotFoundException, …). A Symfony EventListener turns any subclass into
 * an HTTP 404 response.
 */
abstract class EntityNotFoundException extends DomainException
{
}
