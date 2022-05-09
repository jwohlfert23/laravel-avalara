<?php

namespace Jwohlfert23\LaravelAvalara\Models;

class ExposureZone extends BaseModel
{
    public ?int $id;

    public ?int $companyId;

    public ?string $name;

    public ?string $tag;

    public ?string $description;

    public ?string $created;

    public ?string $modified;

    public ?string $region;

    public ?string $country;
}
