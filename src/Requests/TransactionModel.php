<?php

namespace Jwohlfert23\LaravelAvalara\Requests;

use Jwohlfert23\LaravelAvalara\AvalaraDocType;

class TransactionModel extends BaseAvalaraModel
{
    public string $companyCode;

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
        public ?string $customerCode = null,
        public ?\DateTimeInterface $date = null,
        public ?string $code = null,
    ) {
        $this->companyCode = config('avalara.company_code', '');
    }
}
