<?php

namespace Jwohlfert23\LaravelAvalara\Models;

class CreateCertExpressInvitation extends BaseModel
{
    public ?string $recipient;

    public ?string $coverLetterTitle;

    public ?string $deliveryMethod;

    /** @var int[]|null */
    public ?array $exposureZones;

    /** @var int[]|null */
    public ?array $exemptReasons;
}
