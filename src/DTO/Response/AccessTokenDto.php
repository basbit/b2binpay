<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;
use DateTime;

class AccessTokenDto extends BaseDto
{
    public string $refresh;
    public string $access;
    public bool $is2faConfirmed;
    public DateTime $accessExpiredAt;
    public DateTime $refreshExpiredAt;
}