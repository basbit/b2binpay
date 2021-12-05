<?php

namespace B2Binpay\DTO\Request;

use B2Binpay\DTO\BaseDto;

class CalculateFeeDto extends BaseDto
{
    protected float $amount;
    protected string $toAddress;

    public function __construct(float $amount, string $toAddress)
    {
        parent::__construct(['amount' => $amount, 'toAddress' => $toAddress]);
    }
}