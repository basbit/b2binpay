<?php

namespace B2Binpay\DTO\Request;

use B2Binpay\DTO\BaseDto;

class DepositDto extends BaseDto
{
    public string $label;
    public string $trackingId;
    public int $confirmationsNeeded;
    public string $callbackUrl;
    public string $addressType;

    public function __construct(string $label, string $trackingId, int $confirmationsNeeded, string $addressType, string $callbackUrl)
    {
        parent::__construct(
            [
                'label' => $label,
                'trackingId' => $trackingId,
                'confirmationsNeeded' => $confirmationsNeeded,
                'callbackUrl' => $callbackUrl,
                'addressType' => $addressType
            ]
        );
    }

}