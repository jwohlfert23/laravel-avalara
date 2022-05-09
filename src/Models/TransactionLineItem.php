<?php

namespace Jwohlfert23\LaravelAvalara\Models;

class TransactionLineItem extends BaseModel
{
    public ?int $id;
    public ?int $transactionId;
    public ?string $lineNumber;
    public ?string $entityUseCode;
    public ?string $description;
    public ?int $destinationAddressId;
    public ?int $originAddressId;
    public ?float $discountAmount;
    public ?int $discountTypeId;
    public ?float $exemptAmount;
    public ?string $certificateId;
    public ?string $exemptNo;
    public ?bool $isItemTaxable;
    public ?string $itemCode;
    public float $lineAmount;
    public float|int $quantity;
    public ?string $reportingDate;
    public ?float $tax;
    public ?float $taxableAmount;
    public ?float $taxCalculated;
    public ?string $taxCode;
    public ?int $taxCodeId;
    public ?string $taxDate;
    public ?string $taxOverrideType;
    public ?float $taxOverrideAmount;
    public ?string $taxOverrideReason;
    public ?bool $taxIncluded;
}
