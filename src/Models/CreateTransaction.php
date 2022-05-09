<?php

namespace Jwohlfert23\LaravelAvalara\Models;

use Jwohlfert23\LaravelAvalara\AvalaraClient;
use Jwohlfert23\LaravelAvalara\AvalaraDocType;

class CreateTransaction extends BaseModel
{
    /** @var CreateLineItem[] */
    public array $lines = [];

    /** @var Address[] */
    public array $addresses = [];

    public ?TaxOverride $taxOverride = null;

    public string $code;

    public AvalaraDocType $type;

    public string $companyCode;

    public string $date;

    public string $salespersonCode;

    public string $customerCode;

    public string $entityUseCode;

    public float $discount;

    public string $purchaseOrderNo;

    public string $exemptionNo;

    public string $referenceCode;

    public string $reportingLocationCode;

    public bool $commit;

    public string $batchCode;

    public string $currencyCode;

    public string $serviceMode;

    public float $exchangeRate;

    public string $exchangeRateEffectiveDate;

    public string $exchangeRateCurrencyCode;

    public string $posLaneCode;

    public string $businessIdentificationNo;

    public string $isSellerImporterOfRecord;

    public string $description;

    public string $email;

    public function toNestedModel(string $name, array $value)
    {
        if ($name === 'lines') {
            return new CreateLineItem($value);
        }

        if ($name === 'addresses') {
            return new Address($value);
        }

        return null;
    }

    public function create(): Transaction
    {
        return app(AvalaraClient::class)->createTransaction($this);
    }
}
