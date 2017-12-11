<?php

namespace JeroenDesloovere\VCard\Property;

use JeroenDesloovere\VCard\Formatter\Property\AddressFormatter;
use JeroenDesloovere\VCard\Formatter\Property\PropertyFormatterInterface;
use JeroenDesloovere\VCard\PropertyParameter\Type;

class Address implements PropertyInterface
{
    /** @var null|string - The country name in your own language, e.g.: belgië */
    private $countryName;

    /** @var null|string - e.g.: apartment or suite number */
    private $extendedAddress;

    /** @var null|string - e.g.: city */
    private $locality;

    /** @var null|string */
    private $postalCode;

    /** @var null|string */
    private $postOfficeBox;

    /** @var null|string  - e.g.: state or province */
    private $region;

    /** @var null|string */
    private $streetAddress;

    /** @var Type */
    private $type;

    public function __construct(
        ?string $postOfficeBox = null,
        ?string $extendedAddress = null,
        ?string $streetAddress = null,
        ?string $locality = null,
        ?string $region = null,
        ?string $postalCode = null,
        ?string $countryName = null,
        Type $type = null
    ) {
        $this->postOfficeBox = $postOfficeBox;
        $this->extendedAddress = $extendedAddress;
        $this->streetAddress = $streetAddress;
        $this->locality = $locality;
        $this->region = $region;
        $this->postalCode = $postalCode;
        $this->countryName = $countryName;
        $this->type = ($type === null) ? Type::home() : $type;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function getExtendedAddress(): ?string
    {
        return $this->extendedAddress;
    }

    public function getFormatter(): PropertyFormatterInterface
    {
        return new AddressFormatter();
    }

    public function getLocality(): ?string
    {
        return $this->locality;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getPostOfficeBox(): ?string
    {
        return $this->postOfficeBox;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}