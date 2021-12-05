<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;

class PayoutDto extends BaseDto
{
    public ?float $amount;
    public ?int $exp;
    public ?string $address;
    public ?string $tagType;
    public ?string $tag;
    public ?array $destination;
    public ?string $trackingId;
    public ?int $confirmationsNeeded;
    public ?float $feeAmount;
    public bool $isFeeIncluded = false;
    public ?string $message;
    public ?int $status;
    public ?string $callbackUrl;
}