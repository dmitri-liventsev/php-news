<?php

namespace App\Shared\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Domain value object for an uploaded binary blob. Carries everything needed
 * by the application layer to persist the file without leaking Symfony's
 * HttpFoundation\UploadedFile into commands/handlers.
 *
 * Build instances at the boundary (a request DTO, a CLI input) — they're
 * immutable after construction.
 */
final readonly class BinaryFile
{
    public function __construct(
        public string $contents,
        public string $originalName,
        public string $mimeType,
    ) {
        if ($contents === '') {
            throw new InvalidArgumentException('Binary file must not be empty.');
        }
        if (trim($originalName) === '') {
            throw new InvalidArgumentException('Original file name must not be blank.');
        }
        if (trim($mimeType) === '') {
            throw new InvalidArgumentException('Mime type must not be blank.');
        }
    }

    public function size(): int
    {
        return strlen($this->contents);
    }

    /**
     * Extension guessed from the original name (no MIME sniffing). Empty
     * string if the name has no extension.
     */
    public function extension(): string
    {
        return pathinfo($this->originalName, PATHINFO_EXTENSION);
    }

    /**
     * Stem (filename minus extension) — useful for slugging.
     */
    public function basename(): string
    {
        return pathinfo($this->originalName, PATHINFO_FILENAME);
    }
}
