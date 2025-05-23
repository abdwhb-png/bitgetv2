<?php

namespace App\Enums;

enum WalletsEnum: string
{
        // case NAMEINAPP = 'name-in-database';

    case BITCOIN = 'Bitcoin';
    case ETHEREUM = 'Ethereum';
    case USDTTRC20 = 'Tether (TRC20)';
    case USDTERC20 = 'Tether (ERC20)';

    public function name(): string
    {
        return match ($this) {
            static::BITCOIN => 'Bitcoin',
            static::ETHEREUM => 'Ethereum',
            static::USDTTRC20 => 'Tether (TRC20)',
            static::USDTERC20 => 'Tether (ERC20)',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            static::BITCOIN => 'BTC',
            static::ETHEREUM => 'ETH',
            static::USDTTRC20 => 'USDT-TRC20',
            static::USDTERC20 => 'USDT-ERC20',
        };
    }
}