<?php

namespace Jwohlfert23\LaravelAvalara\Responses;

use Jwohlfert23\LaravelAvalara\AvalaraDocType;
use Jwohlfert23\LaravelAvalara\Casters\DocTypeCaster;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class AvalaraTransaction extends DataTransferObject
{
    public ?int $id;
    public ?string $code;
    public ?int $companyId;
    public ?string $date;
    public ?string $status;

    #[CastWith(DocTypeCaster::class)]
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

    /** @var null|AvalaraTransactionLine[] */
    #[CastWith(ArrayCaster::class, itemType: AvalaraTransactionLine::class)]
    public ?array $lines;
}
