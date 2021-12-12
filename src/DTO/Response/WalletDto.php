<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;
use B2Binpay\DTO\RelationshipItemDto;
use DateTime;

class WalletDto extends BaseDto
{
    public int $id;
    public RelationshipItemDto $currency;
    public int $status;
    public DateTime $createdAt;
    public float $balanceConfirmed;
    public float $balancePending;
    public float $balanceUnusable;
    public float $minimalTransferAmount;
    public array $destination;
}