<?php

declare(strict_types=1);

namespace Example\Domain\Invoice;

/**
 *
 */
class Address
{
    public function __construct(
        public readonly string $street,
        public readonly string $city,
        public readonly string $zip,
    )
    {
    }

    /**
     * Create a new Address instance
     *
     * @param string $street
     * @param string $city
     * @param string $zip
     * @return Address
     */
    public static function create(
        string $street,
        string $city,
        string $zip
    ): self {
        return new self($street, $city, $zip);
    }
}
