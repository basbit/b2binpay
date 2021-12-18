<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;

class DepositDto extends BaseDto
{
    public ?int $id;
    public ?string $type;
    public ?string $label;
    public ?float $targetPaid;
    public ?string $paymentPage;
    public ?string $addressType;
    public ?string $trackingId;
    public ?int $confirmationsNeeded;
    public ?int $status = null;
    public ?string $callbackUrl;
    public ?string $address;
    public ?string $message = null;
    public ?array $destination;
    public ?array $assets;
    public ?TransferDto $transfer;
    public ?CurrencyDto $currency;
}