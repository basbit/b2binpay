<?php

namespace B2Binpay\DTO\Request;

use B2Binpay\DTO\BaseDto;

class AccessTokenDto extends BaseDto
{
    protected string $login;
    protected string $password;

    public function __construct(string $login, string $password)
    {
        parent::__construct(['login' => $login, 'password' => $password]);
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}