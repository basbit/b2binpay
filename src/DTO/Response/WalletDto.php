<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;
use DateTime;

class WalletDto extends BaseDto
{
    public int $status;
    public DateTime $createdAt;
    public float $balanceConfirmed;
    public float $balancePending;
    public float $balanceUnusable;
    public float $minimalTransferAmount;
    public array $destination;
}