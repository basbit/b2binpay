<?php

namespace B2Binpay\DTO\Response;

use B2Binpay\DTO\BaseDto;
use DateTime;

class TransferDto extends BaseDto
{
    public int $iso;
    public int $confirmations;
    public int $opId;
    public int $opType;
    public int $riskStatus;
    public int $risk;
    public string $amount;
    public string $commission;
    public string $fee;
    public string $txid;
    public int $status;
    public string $message;
    public string $userMessage;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}