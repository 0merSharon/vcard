<?php

namespace JeroenDesloovere\VCard\Property;

use JeroenDesloovere\VCard\Formatter\Property\NameFormatter;
use JeroenDesloovere\VCard\Formatter\Property\PropertyFormatterInterface;

class Name implements PropertyInterface
{
    /**
     * @var null|string
     */
    private $additional;

    /**
     * @var null|string
     */
    private $firstName;

    /**
     * @var null|string
     */
    private $lastName;

    /**
     * @var null|string
     */
    private $prefix;

    /**
     * @var null|string
     */
    private $suffix;

    public function __construct(
        ?string $lastName = null,
        ?string $firstName = null,
        ?string $additional = null,
        ?string $prefix = null,
        ?string $suffix = null
    ) {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->additional = $additional;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public static function fromVcfString(string $value): self
    {
        @list(
            $firstName,
            $additional,
            $lastName,
            $prefix,
            $suffix
        ) = explode(';', $value);
        return new self(
            ($lastName !== '') ? $lastName : null,
            ($firstName !== '') ? $firstName : null,
            ($additional !== '') ? $additional : null,
            ($prefix !== '') ? $prefix : null,
            ($suffix !== '') ? $suffix : null
        );
    }

    public function getAdditional(): ?string
    {
        return $this->additional;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getFormatter(): PropertyFormatterInterface
    {
        return new NameFormatter($this);
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getNode(): string
    {
        return 'N';
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function isAllowedMultipleTimes(): bool
    {
        return false;
    }
}
