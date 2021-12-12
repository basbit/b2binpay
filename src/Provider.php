<?php

declare(strict_types=1);

namespace B2Binpay;

use B2Binpay\DTO\DataItemDto;
use B2Binpay\DTO\MetaDto;
use B2Binpay\DTO\RelationshipItemDto;
use B2Binpay\DTO\RelationshipsDto;
use B2Binpay\DTO\Request\AccessTokenDto as AccessTokenRequestDto;
use B2Binpay\DTO\Request\CalculateFeeDto;
use B2Binpay\DTO\Request\DepositDto as DepositRequestDto;
use B2Binpay\DTO\Request\PayoutDto as PayoutRequestDto;
use B2Binpay\DTO\Request\RefreshTokenDto;
use B2Binpay\DTO\Response\CurrencyDto;
use B2Binpay\DTO\Response\DepositDto as DepositResponseDto;
use B2Binpay\DTO\Response\PayoutDto as PayoutResponseDto;
use B2Binpay\DTO\Response\PayoutFeeDto;
use B2Binpay\DTO\Response\RateDto;
use B2Binpay\DTO\Response\TransferDto;
use B2Binpay\DTO\Response\WalletDto;
use B2Binpay\DTO\ResponseDto;
use B2Binpay\Enum\AddressType;
use B2Binpay\Exception\IncorrectRatesException;
use B2Binpay\Exception\UpdateTokenException;
use B2Binpay\Exception\WrongCurrencyException;
use DateTime;

/**
 * B2BinPay payment provider
 * https://docs.b2binpay.com/v2/en/api-reference.htm
 *
 * @package B2Binpay
 */
class Provider
{
    private const PRODUCTION_URL = 'https://api.b2binpay.com';
    private const TEST_URL = 'https://api-sandbox.b2binpay.com';
    private const URI_TOKEN = '/token';
    private const URI_REFRESH_TOKEN = '/token/refresh';
    private const URI_WALLETS = '/wallet';
    private const URI_CURRENCY = '/currency';
    private const URI_TRANSFER = '/transfer';
    private const URI_RATES = '/rates';
    private const URI_DEPOSIT = '/deposit';
    private const URI_PAYOUT = '/payout';
    private const URI_CALCULATE_FEE = '/payout/calculate';

    private string $authKey;
    private string $authSecret;
    private ?string $accessToken = null;
    private ?string $refreshToken = null;
    private bool $testing;
    private Request $request;
    private Currency $currency;
    private AmountFactory $amountFactory;
    private string $callbackUrl;
    private DateTime $accessTokenExpired;
    private DateTime $refreshTokenExpired;

    public function __construct(
        string $authKey,
        string $authSecret,
        bool $testing = false,
        Currency $currency = null,
        AmountFactory $amountFactory = null
    ) {
        $this->currency = $currency ?? new Currency();
        $this->amountFactory = $amountFactory ?? new AmountFactory($this->currency);
        $this->authKey = $authKey;
        $this->authSecret = $authSecret;
        $this->request = new Request();
        $this->testing = $testing;
    }

    public function setCallbackUrl(string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }

    public function getAccessToken(): string
    {
        if (null === $this->accessToken) {
            $access = $this->request->token(
                new AccessTokenRequestDto($this->authKey, $this->authSecret),
                $this->getEndpoint(self::URI_TOKEN)
            );
            $this->accessToken = $access->access;
            $this->refreshToken = $access->refresh;
            $this->accessTokenExpired = $access->accessExpiredAt;
            $this->refreshTokenExpired = $access->refreshExpiredAt;
        }

        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function refreshToken(): string
    {
        $this->accessToken = null;
        if (null === $this->refreshToken) {
            $access = $this->request->token(
                new RefreshTokenDto($this->refreshToken),
                $this->getEndpoint(self::URI_REFRESH_TOKEN)
            );
            $this->accessToken = $access->access;
            $this->refreshToken = $access->refresh;
            $this->accessTokenExpired = $access->accessExpiredAt;
            $this->refreshTokenExpired = $access->refreshExpiredAt;
        }

        return $this->getAccessToken();
    }

    public function sendRequest(string $method, string $url, ?DataItemDto $data = null): ResponseDto
    {
        try {
            $result = $this->request->send($this->getAccessToken(), $method, $url, $data);
        } catch (UpdateTokenException $e) {
            $this->accessToken = null;
            $result = $this->request->send($this->getAccessToken(), $method, $url, $data);
        }

        return $result;
    }

    private function getEndpoint(string $uri): string
    {
        $gateway = $this->testing ? self::TEST_URL : self::PRODUCTION_URL;
        return $gateway . $uri;
    }

    /**
     * @return string
     */
    public function getAuthorization(): string
    {
        return 'Basic ' . $this->getAccessToken();
    }

    public function getRates(string $leftCurrency, ?string $rightCurrency = null): array
    {
        $filter = ['left' => $leftCurrency, 'right' => $rightCurrency];

        $url = $this->getEndpoint(self::URI_RATES . '?' . http_build_query(['filter' => $filter]));

        $response = $this->sendRequest('get', $url);

        $rates = [];
        foreach ($response->getData() as $item) {
            $rates[] = new RateDto($item->getAttributes());
        }

        return $rates;
    }

    public function convertCurrency(
        string $sum,
        string $currencyFrom,
        string $currencyTo,
        array $rates = null
    ): string {
        $isoFrom = $this->currency->getIso($currencyFrom);
        $isoTo = $this->currency->getIso($currencyTo);

        $input = $this->amountFactory->create($sum, $isoFrom);

        if ($isoFrom === $isoTo) {
            return $input->getValue();
        }

        $rates = $rates ?? $this->getRates($currencyFrom, $currencyTo);

        $rate = array_reduce(
            $rates,
            function ($carry, $item) use ($isoTo) {
                if ($item->to->iso === $isoTo) {
                    $carry = $this->amountFactory->create($item->rate, null, $item->pow);
                }
                return $carry;
            }
        );

        if (empty($rate)) {
            throw new IncorrectRatesException("Can't get rates to convert from $isoFrom to $isoTo");
        }

        $precision = $this->currency->getPrecision($isoTo);

        return $input->convert($rate, $precision)->getValue();
    }

    public function addMarkup(string $sum, string $currency, int $percent): string
    {
        $iso = $this->currency->getIso($currency);

        $amount = $this->amountFactory->create($sum, $iso);

        return $amount->percentage($percent)->getValue();
    }

    public function getWallet(int $walletId): WalletDto
    {
        $response = $this->sendRequest('get', $this->getEndpoint(self::URI_WALLETS . '/' . $walletId));

        /** @var DataItemDto $data */
        $data = $response->getData()[0];
        $wallet = new WalletDto($data->getAttributes());
        $wallet->id = $data->id;
        if(isset($data->getRelationships()['currency'])) {
            $relationship = $data->getRelationships()['currency']['data'];
            $wallet->currency = new RelationshipItemDto($relationship['id'], $relationship['type']);
        }

        return $wallet;
    }

    public function getCurrency(int $iso): CurrencyDto
    {
        $response = $this->sendRequest('get', $this->getEndpoint(self::URI_CURRENCY . '/' . $iso));

        return new CurrencyDto($response->getData()[0]->getAttributes());
    }

    public function getTransfer(int $transferId): TransferDto
    {
        $url = $this->getEndpoint(self::URI_TRANSFER . '/' . $transferId);
        $response = $this->sendRequest('get', $url);

        return new TransferDto($response->getData()[0]->getAttributes());
    }

    public function getDeposit(int $depositId): DepositResponseDto
    {
        $url = $this->getEndpoint(self::URI_DEPOSIT . '/' . $depositId);
        $response = $this->sendRequest('get', $url);

        return new DepositResponseDto($response->getData()[0]->getAttributes());
    }

    public function createDeposit(
        int $wallet,
        string $currency,
        string $label,
        string $trackingId,
        ?int $confirmationsNeeded = 3
    ): DepositResponseDto {

        $iso = $this->currency->getIso($currency);

        if (!isset(AddressType::DEFAULT_ADDRESSES_BY_ISO[$iso])) {
            throw new WrongCurrencyException();
        }

        $addressType = AddressType::DEFAULT_ADDRESSES_BY_ISO[$iso];

        $url = $this->getEndpoint(self::URI_DEPOSIT);
        $deposit = new DepositRequestDto($label, $trackingId, $confirmationsNeeded, $addressType, $this->callbackUrl);
        $relationships = new RelationshipsDto();
        $relationships->wallet = new RelationshipItemDto($wallet, 'wallet');
        $relationships->currency = new RelationshipItemDto($iso, 'currency');

        $data = DataItemDto::setFromParams(
            "deposit",
            $deposit->toArrayWithSnakeKeys(),
            $relationships->toArrayWithSnakeKeys(),
        );
        $response = $this->sendRequest('post', $url, $data);

        return new DepositResponseDto($response->getData()[0]->getAttributes());
    }

    public function getInvoice(int $depositId): DepositResponseDto
    {
        return $this->getDeposit($depositId);
    }

    public function createInvoice(
        string $wallet,
        string $currency,
        string $label,
        string $trackingId,
        ?int $confirmationsNeeded = 3
    ): DepositResponseDto {
        $iso = $this->currency->getIso($currency);

        if (!isset(AddressType::DEFAULT_ADDRESSES_BY_ISO[$iso])) {
            throw new WrongCurrencyException();
        }

        $addressType = AddressType::DEFAULT_ADDRESSES_BY_ISO[$iso];

        $url = $this->getEndpoint(self::URI_DEPOSIT);
        $deposit = new DepositRequestDto($label, $trackingId, $confirmationsNeeded, $addressType, $this->callbackUrl);
        $relationships = new RelationshipsDto();
        $relationships->wallet = new RelationshipItemDto($wallet, 'wallet');
        $relationships->currency = new RelationshipItemDto($iso, 'currency');
        $data = DataItemDto::setFromParams(
            "deposit",
            $deposit->toArrayWithSnakeKeys(),
            $relationships->toArrayWithSnakeKeys(),
        );
        $response = $this->sendRequest('post', $url, $data);

        $responseData = $response->getData()[0];

        $responseDto = new DepositResponseDto($responseData->getAttributes());
        $responseDto->id = $responseData->id;
        return $responseDto;
    }

    public function processDepositCallback(array $responseData): DepositResponseDto
    {
        $depositDto = new DepositResponseDto($responseData['data']['attributes']);
        $depositDto->id = (int)$responseData['data']['id'];
        $depositDto->type = $responseData['data']['type'];
        $meta = new MetaDto($responseData['meta']);

        foreach ($responseData['included'] as $included) {
            switch ($included['type']) {
                case 'currency':
                    $currencyDto = new CurrencyDto($included['attributes']);
                    $depositDto->currency = $currencyDto;
                    break;
                case 'transfer':
                    $transferDto = new TransferDto($included['attributes']);
                    $depositDto->transfer = $transferDto;
                    break;
            }
        }

        $message = $depositDto->transfer->status . $depositDto->transfer->amount . $depositDto->trackingId . $meta->time;

        $this->request->checkSign($meta->sign, $message, $this->authKey, $this->authSecret);

        return $depositDto;
    }

    public function processInvoiceCallback(array $responseData): DepositResponseDto
    {
        return $this->processDepositCallback($responseData);
    }

    public function getPayout(int $payoutId): PayoutResponseDto
    {
        $url = $this->getEndpoint(self::URI_PAYOUT . '/' . $payoutId);
        $response = $this->sendRequest('get', $url);

        return new PayoutResponseDto($response->getData()[0]->getAttributes());
    }

    public function createPayout(
        int $walletId,
        float $amount,
        float $feeAmount,
        string $address,
        string $trackingId,
        ?int $confirmationsNeeded = 3
    ): PayoutResponseDto {
        $url = $this->getEndpoint(self::URI_PAYOUT);
        $deposit = new PayoutRequestDto(
            $amount,
            $feeAmount,
            $address,
            $trackingId,
            $confirmationsNeeded,
            $this->callbackUrl
        );
        $relationships = new RelationshipsDto();
        $relationships->wallet = new RelationshipItemDto($walletId, 'wallet');
        $data = DataItemDto::setFromParams(
            "payout",
            $deposit->toArrayWithSnakeKeys(),
            $relationships->toArrayWithSnakeKeys()
        );
        $response = $this->sendRequest('post', $url, $data);

        return new PayoutResponseDto($response->getData()[0]->getAttributes());
    }

    public function calculateFee(int $walletId, int $currencyIso, float $amount, string $address): PayoutFeeDto
    {
        $url = $this->getEndpoint(self::URI_CALCULATE_FEE);
        $calculateFeeDto = new CalculateFeeDto(
            $amount,
            $address
        );
        $relationships = new RelationshipsDto();
        $relationships->wallet = new RelationshipItemDto($walletId, 'wallet');
        $relationships->currency = new RelationshipItemDto($currencyIso, 'currency');
        $data = DataItemDto::setFromParams(
            "payout-calculation",
            $calculateFeeDto->toArrayWithSnakeKeys(),
            $relationships->toArrayWithSnakeKeys()
        );
        $response = $this->sendRequest('post', $url, $data);

        return new PayoutFeeDto($response->getData()[0]->getAttributes());
    }

    public function processPayoutCallback(array $responseData): DepositResponseDto
    {
        return $this->processDepositCallback($responseData);
    }

    /**
     * @return DateTime
     */
    public function getAccessTokenExpired(): DateTime
    {
        return $this->accessTokenExpired;
    }

    /**
     * @return DateTime
     */
    public function getRefreshTokenExpired(): DateTime
    {
        return $this->refreshTokenExpired;
    }
}
