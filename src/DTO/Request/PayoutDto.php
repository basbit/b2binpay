<?php

namespace B2Binpay\DTO\Request;

use B2Binpay\DTO\BaseDto;

class PayoutDto extends BaseDto
{
    public float $amount;
    public float $feeAmount;
    public string $address;
    public string $trackingId;
    public int $confirmationsNeeded;
    public string $callbackUrl;

    public function __construct(
        float $amount,
        float $feeAmount,
        string $address,
        string $trackingId,
        int $confirmationsNeeded,
        string $callbackUrl
    ) {
        parent::__construct(
            [
                'amount' => $amount,
                'feeAmount' => $feeAmount,
                'trackingId' => $trackingId,
                'confirmationsNeeded' => $confirmationsNeeded,
                'callbackUrl' => $callbackUrl
            ]
        );
    }

}