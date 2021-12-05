<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;

class PayoutFeeDto extends BaseDto
{
    public ?bool $isInternal;
    public ?array $fee;
    public ?array $commission;
}