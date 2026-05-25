<?php

namespace App\News\Domain\Exception;

use DomainException;

/**
 * Base class for "entity with this id was not found" errors.
 *
 * Concrete subclasses: ArticleNotFoundException, ImageNotFoundException, …
 * A Symfony EventListener turns any EntityNotFoundException into an HTTP 404 response.
 */
abstract class EntityNotFoundException extends DomainException
{
}
