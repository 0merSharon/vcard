<?php

namespace JeroenDesloovere\VCard\Parser;

use JeroenDesloovere\VCard\Exception\ParserException;
use JeroenDesloovere\VCard\Parser\Property\NodeParserInterface;
use JeroenDesloovere\VCard\VCard;

final class VcfParser implements ParserInterface
{
    /**
     * @var array - Structure = [node => NodeParserInterface]
     */
    private $parsers = [];

    /**
     * @param string $content
     * @return VCard[]
     * @throws ParserException
     */
    public function getVCards(string $content): array
    {
        // Set possible parsers
        foreach (VCard::POSSIBLE_VALUES as $propertyClass) {
            $this->parsers[($propertyClass)::getNode()] = ($propertyClass)::getParser();
        }

        $vCards = [];

        foreach ($this->splitIntoVCardsContent($content) as $vCardContent) {
            $vCard = $this->parseVCard($vCardContent);

            if ($vCard instanceof VCard) {
                $vCards[] = $vCard;
            }
        }

        return $vCards;
    }

    private function parseParameters(?string $parameters): array
    {
        if ($parameters === null) {
            return [];
        }

        $parsedParameters = [];
        $parameters = explode(';', $parameters);
        foreach ($parameters as $parameter) {
            @list($node, $value) = explode('=', $parameter, 2);

            if (array_key_exists($node, $this->parsers)) {
                $parsedParameters[$node] = $this->parsers[$node]->parseLine($value);
            }
        }

        return $parsedParameters;
    }

    protected function parseVCard(string $content): VCard
    {
        $vCard = new VCard();
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            // Strip grouping information. We don't use the group names. We
            // simply use a list for entries that have multiple values.
            // As per RFC, group names are alphanumerical, and end with a
            // period (.).
            $line = preg_replace('/^\w+\./', '', trim($line));

            @list($node, $value) = explode(':', $line, 2);
            @list($node, $parameters) = explode(';', $node, 2);

            if (!array_key_exists($node, $this->parsers)) {
                // @todo: add this line to "not converted" errors. Can be useful to improve the parser.

                continue;
            }

            $parameters = $this->parseParameters($parameters);

            try {
                /**
                 * @var NodeParserInterface $this->parsers[$node]
                 */
                $vCard->add($this->parsers[$node]->parseLine($value, $parameters));
            } catch (\Exception $e) {
                // @todo: fetch errors when setting properties that are already set.
            }
        }

        return $vCard;
    }

    protected function splitIntoVCardsContent(string $content): array
    {
        // Normalize new lines.
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        $content = trim($content);

        if (!preg_match('/^BEGIN:VCARD[\s\S]+END:VCARD$/', $content)) {
            throw ParserException::forUnreadableVCard($content);
        }

        // Remove first BEGIN:VCARD and last END:VCARD
        $content = substr($content, 12, -10);

        // RFC2425 5.8.1. Line delimiting and folding
        // Unfolding is accomplished by regarding CRLF immediately followed by
        // a white space character (namely HTAB ASCII decimal 9 or. SPACE ASCII
        // decimal 32) as equivalent to no characters at all (i.e., the CRLF
        // and single white space character are removed).
        $content = preg_replace("/\n(?:[ \t])/", '', $content);

        // If multiple vcards split per vcard
        return preg_split('/\nEND:VCARD\s+BEGIN:VCARD\n/', $content);
    }
}
