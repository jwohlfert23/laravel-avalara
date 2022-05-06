<?php

namespace Jwohlfert23\LaravelAvalara\Requests;

class AddressModel extends BaseAvalaraModel
{
    const TYPE_SHIP_TO = 'ShipTo';
    const TYPE_SHIP_FROM = 'ShipFrom';
    const TYPE_SINGLE_LOCATION = 'SingleLocation';

    public ?int $id;
    public ?int $transactionId;
    public string $line1;
    public string $line2;
    public ?string $line3;
    public string $city;
    public ?string $region;
    public string $postalCode;
    public string $country;
}
