<?php

namespace JeroenDesloovere\VCard\Property;

use JeroenDesloovere\VCard\Formatter\Property\NoteFormatter;
use JeroenDesloovere\VCard\Formatter\Property\PropertyFormatterInterface;

final class Note implements PropertyInterface
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromVcfString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFormatter(): PropertyFormatterInterface
    {
        return new NoteFormatter($this);
    }

    public function getNode(): string
    {
        return 'NOTE';
    }

    public function isAllowedMultipleTimes(): bool
    {
        return false;
    }
}
