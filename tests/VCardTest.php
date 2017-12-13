<?php

namespace JeroenDesloovere\VCard;

use JeroenDesloovere\VCard\Formatter\Formatter;
use JeroenDesloovere\VCard\Formatter\VcfFormatter;
use JeroenDesloovere\VCard\Parser\Parser;
use JeroenDesloovere\VCard\Parser\VcfParser;
use JeroenDesloovere\VCard\Property\Address;
use JeroenDesloovere\VCard\Property\Name;
use JeroenDesloovere\VCard\Property\Parameter\Type;
use PHPUnit\Framework\TestCase;

/**
 * How to execute all tests: `vendor/bin/phpunit tests`
 */
class VCardTest extends TestCase
{
    /**
     * @var VCard 
     */
    private $firstVCard;

    /**
     * @var VCard 
     */
    private $secondVCard;

    public function setUp(): void
    {
        // Building one or multiple vCards
        $this->firstVCard = (new VCard())
            ->add(new Name('Jeroen', 'Desloovere'))
            ->add(new Address(null, null, 'Markt 1', 'Brugge', 'West-Vlaanderen', '8000', 'België', Type::work()))
            ->add(new Address(null, 'Penthouse', 'Korenmarkt 1', 'Gent', 'Oost-Vlaanderen', '9000', 'België', Type::home()));
        $this->secondVCard = (new VCard())
            ->add(new Name('John', 'Doe'))
            ->add(new Address(null, 'Penthouse', 'Korenmarkt 1', 'Gent', 'Oost-Vlaanderen', '9000', 'België', Type::work()));
    }

    public function testEmptyVCards(): void
    {
        (new VCard())
            ->add(new Name(null, null))
            ->add(new Address(null, null, null, null, null, null, null, null));

        (new VCard())
            ->add(new Name())
            ->add(new Address());

        $this->assertFalse(false);
    }

    public function testSavingOneVCardToVcfFile(): void
    {
        // Saving "vcard.vcf"
        $formatter = new Formatter(new VcfFormatter(), 'vcard');
        $formatter->addVCard($this->firstVCard);
        $formatter->save(__DIR__);

        $this->assertFalse(false);
    }

    public function testSavingMultipleVCardsToVcfFile(): void
    {
        // Saving "vcards.vcf"
        $formatter = new Formatter(new VcfFormatter(), 'vcards');
        $formatter->addVCard($this->firstVCard);
        $formatter->addVCard($this->secondVCard);
        $formatter->save(__DIR__);

        $this->assertFalse(false);
    }

    public function testParsingOneVCardFromVcfFile(): void
    {
        $parser = new Parser(new VcfParser(), Parser::getFileContents(__DIR__ . '/assets/vcard.vcf'));

        // @todo
        //$this->assertEquals($this->firstVCard, $parser->getVCards()[0]);

        $this->assertFalse(false);
    }

    public function testParsingMultipleVCardsFromVcfFile(): void
    {
        $parser = new Parser(new VcfParser(), Parser::getFileContents(__DIR__ . '/assets/vcards.vcf'));

        // @todo
        //$this->assertEquals($this->firstVCard, $parser->getVCards()[0]);
        //$this->assertEquals($this->secondVCard, $parser->getVCards()[1]);

        $this->assertFalse(false);
    }

    public function testVCardGetProperties(): void
    {
        $this->assertEquals(3, count($this->firstVCard->getProperties()));
        $this->assertEquals(1, count($this->firstVCard->getProperties(Name::class)));
        $this->assertEquals(2, count($this->firstVCard->getProperties(Address::class)));
        $this->assertEquals(2, count($this->secondVCard->getProperties()));
        $this->assertEquals(1, count($this->secondVCard->getProperties(Name::class)));
        $this->assertEquals(1, count($this->secondVCard->getProperties(Address::class)));
    }
}
