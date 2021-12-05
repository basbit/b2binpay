<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;
use DateTime;

class RateDto extends BaseDto
{
    public string $left;
    public string $right;
    public float $bid;
    public float $ask;
    public float $exp;
    public DateTime $expiredAt;
    public DateTime $createdAt;
}