<?php

namespace Jwohlfert23\LaravelAvalara\Models;

class Address extends BaseModel
{
    public const TYPE_SHIP_TO = 'ShipTo';

    public const TYPE_SHIP_FROM = 'ShipFrom';

    public const TYPE_SINGLE_LOCATION = 'SingleLocation';

    public ?int $id;

    public ?int $transactionId;

    public ?string $line1;

    public ?string $line2;

    public ?string $line3;

    public ?string $city;

    public ?string $region;

    public ?string $postalCode;

    public ?string $country;
}
