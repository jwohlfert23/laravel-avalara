<?php

namespace Jwohlfert23\LaravelAvalara\Models;

use Carbon\Carbon;

class TaxOverride extends BaseModel
{
    public string $type;
    public ?float $taxAmount;
    public ?Carbon $taxDate;
    public ?string $reason;

    public static function taxDate(Carbon $taxDate, string $reason = null)
    {
        $model = new TaxOverride();
        $model->type = 'TaxDate';
        $model->taxDate = $taxDate;
        $model->reason = $reason;

        return $model;
    }
}
