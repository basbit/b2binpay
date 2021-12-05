<?php

namespace B2Binpay\DTO\Request;

use B2Binpay\DTO\BaseDto;

class RefreshTokenDto extends BaseDto
{
    protected string $refresh;

    public function __construct(string $refresh)
    {
        parent::__construct(['refresh' => $refresh]);
    }
}