<?php namespace Jwohlfert23\LaravelAvalara\Casters;

use Jwohlfert23\LaravelAvalara\AvalaraDocType;
use Spatie\DataTransferObject\Caster;

class DocTypeCaster implements Caster
{
    /**
     * @param  string  $value
     *
     * @return AvalaraDocType
     */
    public function cast(mixed $value): AvalaraDocType
    {
        return AvalaraDocType::from($value);
    }
}
