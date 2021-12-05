<?php

declare(strict_types=1);

namespace B2Binpay;

use B2Binpay\DTO\BaseDto;
use B2Binpay\DTO\DataItemDto;
use B2Binpay\DTO\MetaDto;
use B2Binpay\DTO\Request\AccessTokenDto as AccessTokenRequestDto;
use B2Binpay\DTO\Response\AccessTokenDto as AccessTokenResponseDto;
use B2Binpay\DTO\ResponseDto;
use B2Binpay\Exception\ConnectionErrorException;
use B2Binpay\Exception\EmptyResponseException;
use B2Binpay\Exception\IncorrectSignatureException;
use B2Binpay\Exception\ServerApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Ramsey\Uuid\Uuid;

/**
 * Send and validate requests through GuzzleHttp
 *
 * @package B2Binpay
 */
class Request
{
    private const ALGORITHM = 'SHA256';

    /**
     * @var Client
     */
    private Client $client;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * @param string $token
     * @param string $method
     * @param string $url
     * @param DataItemDto|null $data
     * @return mixed
     */
    public function send(string $token, string $method, string $url, ?DataItemDto $data = null): ResponseDto
    {
        $header = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/vnd.api+json',
            'Idempotency-Key' => (string)Uuid::uuid4()
        ];

        $request = [
            RequestOptions::VERIFY => false,
            RequestOptions::HEADERS => $header,
            RequestOptions::HTTP_ERRORS => false,
        ];

        if (null !== $data) {
            $request[RequestOptions::JSON] = ['data' => $data->toArrayWithSnakeKeys()];
        }

        return $this->execute($method, $url, $request);
    }

    /**
     * @param BaseDto $tokenDto
     * @param string $url
     * @return AccessTokenResponseDto
     */
    public function token(BaseDto $tokenDto, string $url): AccessTokenResponseDto
    {
        $header = [
            'Content-Type' => 'application/vnd.api+json'
        ];

        $data = DataItemDto::setFromParams("auth-token", $tokenDto->toArrayWithSnakeKeys());
        $request = [
            RequestOptions::VERIFY => false,
            RequestOptions::HEADERS => $header,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON => ['data' => $data->toArrayWithSnakeKeys()],
        ];

        $method = 'POST';
        $responseDecode = $this->execute($method, $url, $request);

        if (null === $responseDecode->getData() || null === $responseDecode->getMeta()) {
            throw new EmptyResponseException($url);
        }

        $data = new AccessTokenResponseDto($responseDecode->getData()[0]->getAttributes());
        $message = $responseDecode->getMeta()->time . $data->refresh;

        if ($tokenDto instanceof AccessTokenRequestDto) {
            $this->checkSign(
                $responseDecode->getMeta()->sign,
                $message,
                $tokenDto->getLogin(),
                $tokenDto->getPassword()
            );
        }

        return $data;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $request
     * @return ResponseDto
     */
    private function execute(string $method, string $url, array $request): ResponseDto
    {
        try {
            $response = $this->client->request($method, $url, $request);
        } catch (GuzzleException $e) {
            throw new ConnectionErrorException($e);
        }

        $status = $response->getStatusCode();
        $body = (string)$response->getBody();
        $responseDecode = json_decode($body, true);

        if (empty($responseDecode)) {
            throw new EmptyResponseException($url);
        }

        if (!empty($responseDecode['errors'])) {
            throw new ServerApiException($responseDecode['errors'], $status);
        }

        $meta = isset($responseDecode['meta']) ? new MetaDto($responseDecode['meta']) : null;

        return new ResponseDto($this->prepareData($responseDecode['data']), $meta);
    }

    private function prepareData(?array $responseData = null): ?array
    {
        $data = null;
        if (isset($responseData, $responseData[0])) {
            foreach ($responseData as $responseItem) {
                $data[] = new DataItemDto($responseItem);
            }
        } elseif (isset($responseData)) {
            $data = [new DataItemDto($responseData)];
        }

        return $data;
    }

    public function checkSign(string $sign, string $message, string $login, string $password): void
    {
        $secret = hash(self::ALGORITHM, $login . $password, true);
        $generatedSign = hash_hmac(self::ALGORITHM, $message, $secret);
        if ($sign !== $generatedSign) {
            throw new IncorrectSignatureException();
        }
    }
}
