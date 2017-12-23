<?php

namespace JeroenDesloovere\VCard\Parser\Property;

use JeroenDesloovere\VCard\Property\Gender;
use JeroenDesloovere\VCard\Property\NodeInterface;
use JeroenDesloovere\VCard\Property\Parameter\GenderType;

final class GenderParser extends PropertyParser implements NodeParserInterface
{
    public function parseLine(string $value, array $parameters = []): NodeInterface
    {
        @list(
            $gender,
            $note
        ) = explode(';', $value);

        $this->convertEmptyStringToNull([
            $note
        ]);

        return new Gender(new GenderType($gender), $note);
    }
}
