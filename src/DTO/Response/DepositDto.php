<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;

class DepositDto extends BaseDto
{
    public ?string $label;
    public ?float $targetPaid;
    public ?string $paymentPage;
    public ?string $addressType;
    public ?string $trackingId;
    public ?int $confirmationsNeeded;
    public ?string $callbackUrl;
    public ?string $address;
    public ?string $message;
    public ?array $destination;
    public ?array $assets;
}