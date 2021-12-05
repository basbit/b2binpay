<?php

namespace B2Binpay\DTO;

class RelationshipsDto extends BaseDto
{
    public ?BaseDto $currency;
    public ?BaseDto $parent;
    public ?BaseDto $wallet;
    public ?BaseDto $transfer;
}