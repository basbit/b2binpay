<?php

class AddressType
{
    public const BTC_LEGACY = 'legacy';
    public const BTC_DEFAULT = 'p2sh-segwit';

    public const USDT_OMNI_LEGACY = 'legacy';
    public const USDT_OMNI_DEFAULT = 'p2sh-segwit';

    public const LTC_LEGACY = 'legacy';
    public const LTC_SEGWIT = 'p2sh-segwit';
    public const LTC_DEFAULT = 'bech32';

    public const BCH_LEGACY = 'legacy';
    public const BCH_DEFAULT = 'cash';

    public const XRP_LEGACY = 'address';
    public const XRP_DEFAULT = 'x-address';

    public const DEFAULT_ADDRESSES_BY_ISO = [
        1000 => self::BTC_DEFAULT,
        2005 => self::USDT_OMNI_DEFAULT,
        1003 => self::LTC_DEFAULT,
        1006 => self::BCH_DEFAULT,
        1010 => self::XRP_DEFAULT,
    ];
}