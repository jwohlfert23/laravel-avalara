<?php

namespace Jwohlfert23\LaravelAvalara\Models;

class Certificate extends BaseModel
{
    public ?int $id;
    public ?string $signedDate;
    public ?string $expirationDate;
    public ?string $filename;
    public ?bool $valid;
    public ?bool $verified;
    public ?string $exemptionNumber;
    public ?ExemptionReason $exemptionReason;
    public ?ExposureZone $exposureZone;
    public ?string $createdDate;
    public ?string $modifiedDate;
}
