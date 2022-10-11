<?php

namespace Jwohlfert23\LaravelAvalara\Models;

class CreateLineItem extends BaseModel
{
    public int $quantity;

    public float $amount;

    public string $number;

    public string $taxCode;

    public string $itemCode;

    public string $exemptionCode;

    public bool $discounted;

    public bool $taxIncluded;

    public string $revenueAccount;

    public string $ref1;

    public string $ref2;

    public string $description;

    /** @var Address[] */
    public ?array $addresses;
}
