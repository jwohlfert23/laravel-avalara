<?php

namespace App\Services\Avalara\Models;

use App\Services\Avalara\AvalaraDocType;

class TransactionModel extends BaseAvalaraModel
{
    public ?float $discount = null;

    public ?string $purchaseOrderNo = null;

    /** @var LineItemModel[] */
    public array $lines = [];

    /** @var AddressModel[] */
    public array $addresses = [];

    public bool $commit = true;

    public string $currencyCode = 'USD';

    public ?string $description = null;

    public ?string $email = null;

    public ?TaxOverrideModel $taxOverride = null;

    public function __construct(
        public AvalaraDocType $type,
        public string $companyCode,
        public string $customerCode,
        public \DateTimeInterface $date,
        public ?string $code = null,
    ) {

    }
}
