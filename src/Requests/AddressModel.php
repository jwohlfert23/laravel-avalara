<?php

namespace App\Services\Avalara\Models;

class AddressModel extends BaseAvalaraModel
{
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
