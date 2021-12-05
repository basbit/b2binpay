<?php

namespace B2Binpay\Exception;

class ServerApiException extends B2BinpayException
{
    public function __construct(array $errors, $status)
    {
        $message = [];
        foreach ($errors as $error) {
            $message[] = 'Code: '. $error->code . ', Details: ' . $error->detail;
        }

        parent::__construct("Server returned $status status with error: ". implode(', ', $message));
    }
}
