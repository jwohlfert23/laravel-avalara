<?php

namespace Jwohlfert23\LaravelAvalara\Models;

class Customer extends BaseModel
{
    public ?int $id;
    public int $companyId;
    public ?string $customerCode;
    public ?string $alternateId;
    public ?string $name;
    public ?string $attnName;
    public ?string $line1;
    public ?string $line2;
    public ?string $city;
    public ?string $postalCode;
    public ?string $phoneNumber;
    public ?string $faxNumber;
    public ?string $emailAddress;
    public ?string $contactName;
    public ?string $lastTransaction;
    public ?string $createdDate;
    public ?string $modifiedDate;
    public ?string $country;
    public ?string $region;
    public ?bool $isBill;
    public ?bool $isShip;
}
