<?php

namespace JeroenDesloovere\VCard\Property\Parameter;

class Type implements PropertyParameterInterface
{
    protected const HOME = 'Home';
    protected const WORK = 'Work';

    public const POSSIBLE_VALUES = [
        self::HOME,
        self::WORK,
    ];

    private $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::POSSIBLE_VALUES, true)) {
            throw new \RuntimeException(
                'The given type "'.$value.'" is not allowed. Possible values are: '.implode(', ', self::POSSIBLE_VALUES)
            );
        }

        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function getNode(): string
    {
        return 'TYPE';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function home(): Type
    {
        return new Type(self::HOME);
    }

    public function isHome(): bool
    {
        return $this->value === self::HOME;
    }

    public static function work(): Type
    {
        return new Type(self::WORK);
    }

    public function isWork(): bool
    {
        return $this->value === self::WORK;
    }
}
