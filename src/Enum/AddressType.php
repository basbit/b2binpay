<?php

namespace B2Binpay\Enum;

class AddressType
{
    public const LEGACY = 'legacy';

    public const BTC_DEFAULT = 'p2sh-segwit';

    public const USDT_OMNI_DEFAULT = 'p2sh-segwit';

    public const LTC_SEGWIT = 'p2sh-segwit';
    public const LTC_DEFAULT = 'bech32';

    public const BCH_DEFAULT = 'cash';

    public const XRP_LEGACY = 'address';
    public const XRP_DEFAULT = 'x-address';

    public const DEFAULT_ADDRESSES_BY_ISO = [
        1000 => self::LEGACY,
        2005 => self::LEGACY,
        1003 => self::LEGACY,
        1006 => self::LEGACY,
        1010 => self::XRP_LEGACY,
    ];
}