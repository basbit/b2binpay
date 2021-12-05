<?php

namespace B2Binpay\Exception;

class ServerApiException extends B2BinpayException
{
    public function __construct(array $errors, $status)
    {
        $message = [];
        foreach ($errors as $error) {
            $sources = null;
            if (isset($error['source'])) {
                $sources = implode(', ', $error['source']);
            }
            $message[] = 'Code: ' . ($error['code'] ?? null) . ', Details: ' . ($error['detail'] ?? null) . ' ' . $sources;
        }

        parent::__construct("Server returned $status status with error: " . implode(', ', $message));
    }
}
