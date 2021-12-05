<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;

class CurrencyDto extends BaseDto
{
    public int $iso;
    public string $name;
    public string $alpha;
    public string $alias;
    public int $exp;
    public int $confirmationBlocks;
    public float $minimalTransferAmount;
    public int $block_delay;
}