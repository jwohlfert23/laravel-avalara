<?php namespace Jwohlfert23\LaravelAvalara\Models;

class CertExpressInvitation extends BaseModel
{
    public int $id;

    public string $customerCode;

    public string $deliveryMethod;

    public string $status = 'Ready';

    public ?string $requestLink = null;
}
