<?php

namespace Jwohlfert23\LaravelAvalara\Requests;

class LineItemModel extends BaseAvalaraModel
{
    public ?string $exemptionCode;

    public function __construct(
        public string $number,
        public float $amount,
        public float|int $quantity,
        public ?string $taxCode = null,
        public ?string $itemCode = null,
        public ?string $description = null
    ) {
    }
}
