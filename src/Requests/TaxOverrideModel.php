<?php

namespace Jwohlfert23\LaravelAvalara\Requests;

use Carbon\Carbon;

class TaxOverrideModel extends BaseAvalaraModel
{
    public string $type;
    public ?float $taxAmount;
    public ?Carbon $taxDate;
    public ?string $reason;

    public static function taxDate(Carbon $taxDate, string $reason = null)
    {
        $model = new TaxOverrideModel();
        $model->type = 'TaxDate';
        $model->taxDate = $taxDate;
        $model->reason = $reason;

        return $model;
    }
}
