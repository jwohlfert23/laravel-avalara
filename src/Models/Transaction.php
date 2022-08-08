<?php

namespace Jwohlfert23\LaravelAvalara\Models;

use Jwohlfert23\LaravelAvalara\AvalaraDocType;

class Transaction extends BaseModel
{
    public ?int $id;

    public ?string $code;

    public ?int $companyId;

    public ?string $date;

    public ?string $status;

    public ?AvalaraDocType $type;

    public ?string $currencyCode;

    public ?string $customerCode;

    public ?string $exemptNo;

    public ?string $purchaseOrderNo;

    public ?string $referenceCode;

    public ?string $taxOverrideType;

    public ?float $taxOverrideAmount;

    public ?string $taxOverrideReason;

    public ?float $totalAmount;

    public ?float $totalExempt;

    public ?float $totalDiscount;

    public ?float $totalTax;

    public ?float $totalTaxable;

    public ?float $totalTaxCalculated;

    public ?string $adjustmentReason;

    public ?string $adjustmentDescription;

    public ?bool $locked;

    public ?string $region;

    public ?string $country;

    public ?int $version;

    public ?int $originAddressId;

    public ?int $destinationAddressId;

    public ?string $description;

    public ?string $email;

    public ?string $taxDate;

    /** @var null|TransactionLineItem[] */
    public array $lines;

    public function toNestedModel(string $name, array $value)
    {
        if ($name === 'lines') {
            return new TransactionLineItem($value);
        }

        return null;
    }
}
