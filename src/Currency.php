<?php

declare(strict_types=1);

namespace B2Binpay;

use B2Binpay\Exception\UnknownValueException;

/**
 * Currency
 *
 * @package B2Binpay
 */
class Currency
{
    private const MAX_PRECISION = 18;

    private const LIST = [
        1000 => [
            'iso' => 1000,
            'name' => 'Bitcoin',
            'alpha' => 'BTC',
            'precision' => 8
        ],
        1002 => [
            'iso' => 1002,
            'name' => 'Ethereum',
            'alpha' => 'ETH',
            'precision' => 18
        ],
        1003 => [
            'iso' => 1003,
            'name' => 'Litecoin',
            'alpha' => 'LTC',
            'precision' => 8
        ],
        1005 => [
            'iso' => 1005,
            'name' => 'DASH',
            'alpha' => 'DASH',
            'precision' => 8
        ],
        1006 => [
            'iso' => 1006,
            'name' => 'Bitcoin Cash',
            'alpha' => 'BCH',
            'precision' => 8
        ],
        1007 => [
            'iso' => 1007,
            'name' => 'Monero',
            'alpha' => 'XMR',
            'precision' => 12
        ],
        1010 => [
            'iso' => 1010,
            'name' => 'Ripple',
            'alpha' => 'XRP',
            'precision' => 6
        ],
        1019 => [
            'iso' => 1019,
            'name' => 'Dogecoin',
            'alpha' => 'DOGE',
            'precision' => 8
        ],
        1020 => [
            'iso' => 1020,
            'name' => 'Zcash',
            'alpha' => 'ZEC',
            'precision' => 8
        ],
        1021 => [
            'iso' => 1021,
            'name' => 'Stellar',
            'alpha' => 'XLM',
            'precision' => 7
        ],
        1026 => [
            'iso' => 1026,
            'name' => 'Tron',
            'alpha' => 'TRX',
            'precision' => 6
        ],
        1125 => [
            'iso' => 1125,
            'name' => 'Binance Coin based on BSC',
            'alpha' => 'BNB-BSC',
            'alias' => 'BNB',
            'precision' => 18
        ],
        2005 => [
            'iso' => 2005,
            'name' => 'TetherUS based on OMNI',
            'alpha' => 'USDT-OMNI',
            'alias' => 'USDT',
            'precision' => 8
        ],
        2014 => [
            'iso' => 2014,
            'name' => 'USD Coin based on ERC20',
            'alpha' => 'USDC-ETH',
            'alias' => 'USDC',
            'precision' => 6
        ],
        2015 => [
            'iso' => 2015,
            'name' => 'TetherUS based on ERC20',
            'alpha' => 'USDT-ETH',
            'alias' => 'USDT',
            'precision' => 6
        ],
        2021 => [
            'iso' => 2021,
            'name' => 'Pax Dollar',
            'alpha' => 'USDP-ETH',
            'alias' => 'USDP',
            'precision' => 18
        ],
        2022 => [
            'iso' => 2022,
            'name' => 'TrueUSD',
            'alpha' => 'TUSD-ETH',
            'alias' => 'TUSD',
            'precision' => 18
        ],
        2025 => [
            'iso' => 2025,
            'name' => 'Binance Coin based on DEX',
            'alpha' => 'BNB-DEX',
            'alias' => 'BNB',
            'precision' => 8
        ],
        2065 => [
            'iso' => 2065,
            'name' => 'TetherUS',
            'alpha' => 'BUSD-T-BSC',
            'alias' => 'USDT',
            'precision' => 18
        ],
        2068 => [
            'iso' => 2068,
            'name' => 'Dai',
            'alpha' => 'DAI-ETH',
            'alias' => 'DAI',
            'precision' => 18
        ],
        2077 => [
            'iso' => 2077,
            'name' => 'Binance USD',
            'alpha' => 'BUSD-ETH',
            'alias' => 'BUSD',
            'precision' => 18
        ],
        2085 => [
            'iso' => 2085,
            'name' => 'Chainlink',
            'alpha' => 'LINK-ETH',
            'alias' => 'LINK',
            'precision' => 18
        ],
        2100 => [
            'iso' => 2100,
            'name' => 'Binance USD',
            'alpha' => 'BUSD-BSC',
            'alias' => 'BUSD',
            'precision' => 18
        ],
        2101 => [
            'iso' => 2101,
            'name' => 'Dai',
            'alpha' => 'DAI-BSC',
            'alias' => 'DAI',
            'precision' => 18
        ],
        2102 => [
            'iso' => 2102,
            'name' => 'USD Coin',
            'alpha' => 'USDC-BSC',
            'alias' => 'USDC',
            'precision' => 18
        ],
        2103 => [
            'iso' => 2103,
            'name' => 'Pax Dollar',
            'alpha' => 'USDP-BSC',
            'alias' => 'USDP',
            'precision' => 18
        ],
        2108 => [
            'iso' => 2108,
            'name' => 'Band Protocol',
            'alpha' => 'BAND-ETH',
            'alias' => 'BAND',
            'precision' => 18
        ],
        2112 => [
            'iso' => 2112,
            'name' => 'Compound',
            'alpha' => 'COMP-ETH',
            'alias' => 'COMP',
            'precision' => 18
        ],
        2113 => [
            'iso' => 2113,
            'name' => 'Decentraland',
            'alpha' => 'MANA-ETH',
            'alias' => 'MANA',
            'precision' => 18
        ],
        2115 => [
            'iso' => 2115,
            'name' => 'Loopring',
            'alpha' => 'LRC-ETH',
            'alias' => 'LRC',
            'precision' => 18
        ],
        2117 => [
            'iso' => 2117,
            'name' => 'NuCypher',
            'alpha' => 'NU-ETH',
            'alias' => 'NU',
            'precision' => 18
        ],
        2126 => [
            'iso' => 2126,
            'name' => 'Synthetix',
            'alpha' => 'SNX-ETH',
            'alias' => 'SNX',
            'precision' => 18
        ],
        2129 => [
            'iso' => 2129,
            'name' => 'Uniswap',
            'alpha' => 'UNI-ETH',
            'alias' => 'UNI',
            'precision' => 18
        ],
        2132 => [
            'iso' => 2132,
            'name' => 'Band Protocol',
            'alpha' => 'BAND-BSC',
            'alias' => 'BAND',
            'precision' => 18
        ],
        2133 => [
            'iso' => 2133,
            'name' => 'Basic Attention Token',
            'alpha' => 'BAT-BSC',
            'alias' => 'BAT',
            'precision' => 18
        ],
        2134 => [
            'iso' => 2134,
            'name' => 'Chainlink',
            'alpha' => 'LINK-BSC',
            'alias' => 'LINK',
            'precision' => 18
        ],
        2135 => [
            'iso' => 2135,
            'name' => 'Maker',
            'alpha' => 'MKR-BSC',
            'alias' => 'MKR',
            'precision' => 18
        ],
        2137 => [
            'iso' => 2137,
            'name' => 'SushiSwap',
            'alpha' => 'SUSHI-BSC',
            'alias' => 'SUSHI',
            'precision' => 18
        ],
        2139 => [
            'iso' => 2139,
            'name' => 'Synthetix',
            'alpha' => 'SNX-BSC',
            'alias' => 'SNX',
            'precision' => 18
        ],
        2140 => [
            'iso' => 2140,
            'name' => 'Uniswap',
            'alpha' => 'UNI-BSC',
            'alias' => 'UNI',
            'precision' => 18
        ],
        2141 => [
            'iso' => 2141,
            'name' => 'yearn.finance',
            'alpha' => 'YFI-BSC',
            'alias' => 'YFI',
            'precision' => 18
        ],
        2142 => [
            'iso' => 2142,
            'name' => 'USD Coin based on TRC20',
            'alpha' => 'USDC-TRX',
            'alias' => 'USDC',
            'precision' => 6
        ],
        2145 => [
            'iso' => 2145,
            'name' => 'TetherUS based on TRC20',
            'alpha' => 'USDT-TRX',
            'alias' => 'USDT',
            'precision' => 6
        ],
        2942 => [
            'iso' => 2942,
            'name' => 'yearn.finance',
            'alpha' => 'YFI-ETH',
            'alias' => 'YFI',
            'precision' => 18
        ],
        2946 => [
            'iso' => 2946,
            'name' => 'Maker',
            'alpha' => 'MKR-ETH',
            'alias' => 'MKR',
            'precision' => 18
        ],
        2947 => [
            'iso' => 2947,
            'name' => 'PancakeSwap',
            'alpha' => 'CAKE-BSC',
            'alias' => 'CAKE',
            'precision' => 18
        ],
        2948 => [
            'iso' => 2948,
            'name' => '0x',
            'alpha' => 'ZRX-ETH',
            'alias' => 'ZRX',
            'precision' => 18
        ],
        2955 => [
            'iso' => 2955,
            'name' => 'Basic Attention Token',
            'alpha' => 'BAT-ETH',
            'alias' => 'BAT',
            'precision' => 18
        ],
        2956 => [
            'iso' => 2956,
            'name' => 'Ren',
            'alpha' => 'REN-ETH',
            'alias' => 'REN',
            'precision' => 18
        ],
        2961 => [
            'iso' => 2961,
            'name' => 'SushiSwap',
            'alpha' => 'SUSHI-ETH',
            'alias' => 'SUSHI',
            'precision' => 18
        ],
        2962 => [
            'iso' => 2962,
            'name' => 'The Graph',
            'alpha' => 'GRT-ETH',
            'alias' => 'GRT',
            'precision' => 18
        ],
        2964 => [
            'iso' => 2964,
            'name' => 'Aave',
            'alpha' => 'AAVE-ETH',
            'alias' => 'AAVE',
            'precision' => 18
        ],
        2982 => [
            'iso' => 2982,
            'name' => 'Polygon',
            'alpha' => 'MATIC-ETH',
            'alias' => 'MATIC',
            'precision' => 18
        ],
    ];

    /**
     * @param int $iso
     * @return string
     * @throws UnknownValueException
     */
    public function getAlpha(int $iso): string
    {
        if (!array_key_exists($iso, self::LIST)) {
            throw new UnknownValueException($iso);
        }
        return self::LIST[$iso]['alpha'];
    }

    /**
     * @param int $iso
     * @return string
     * @throws UnknownValueException
     */
    public function getAliasOrAlpha(int $iso): string
    {
        if (!array_key_exists($iso, self::LIST)) {
            throw new UnknownValueException($iso);
        }
        return self::LIST[$iso]['alias'] ?: self::LIST[$iso]['alpha'];
    }

    /**
     * @param string $alpha
     * @return int
     * @throws UnknownValueException
     */
    public function getIso(string $alpha): int
    {
        $alpha = strtoupper($alpha);

        $iso = array_reduce(
            self::LIST,
            function ($carry, $item) use ($alpha) {
                $nodeList = $item['node'] ?? [];
                $nodeList = array_map('strtoupper', $nodeList);

                if ($item['alpha'] === $alpha || in_array($alpha, $nodeList)) {
                    $carry = $item['iso'];
                }
                return (int)$carry;
            }
        );

        if (empty($iso)) {
            throw new UnknownValueException($alpha);
        }

        return $iso;
    }

    /**
     * @param int $iso
     * @return int
     * @throws UnknownValueException
     */
    public function getPrecision(int $iso): int
    {
        if (!array_key_exists($iso, self::LIST)) {
            throw new UnknownValueException($iso);
        }

        return self::LIST[$iso]['precision'];
    }

    /**
     * @return int
     */
    public function getMaxPrecision(): int
    {
        return self::MAX_PRECISION;
    }

    /**
     * @param int $iso
     * @return string
     * @throws UnknownValueException
     */
    public function getName(int $iso): string
    {
        if (!array_key_exists($iso, self::LIST)) {
            throw new UnknownValueException($iso);
        }

        return self::LIST[$iso]['name'];
    }
}
