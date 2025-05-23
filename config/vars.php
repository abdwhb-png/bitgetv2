<?php
return [
    'admin_subdomain' => 'admin.',
    'app_subdomain' => 'app.',

    'identification_ids' => [
        'ID Card',
        'Passport',
        'Driver\'s License',
        'Utility Bill',
        'Cit
        izen ID',
    ],

    'transaction_types' => [
        'deposit',
        'withdrawal',
        'swap',
    ],

    'profit_types' => [
        'positive' => 'Positive',
        'negative' => 'Negative',
        'break-even' => 'Break Even',
    ],

    'verification_types' => ['email', 'phone', 'kyc'],

    'statuses' => [
        [
            'label' => 'not provided',
            'value' => -100,
        ],
        [
            'label' => 'rejected',
            'value' => -1,
        ],
        [
            'label' => 'pending',
            'value' => 0,
        ],
        [
            'label' => 'approved',
            'value' => 1,
        ],
        [
            'label' => 'verified',
            'value' => true,
        ],
        [
            'label' => 'unverified',
            'value' => false,
        ],
    ],

    'order_statuses' => ['opened', 'closed'],
    'transaction_statuses' => ['pending', 'approved', 'rejected'],
    'email_statuses' => ["verified", "unverified"],
    'kyc_statuses' => ["approved", "pending", "rejected"],

    'good_symbols' => [
        "USDTBTC" => "BTCUSDT",
        "USDTETH" => "ETHUSDT",
        "USDT-TRC20BTC" => "BTCUSDT",
        "USDT-TRC20ETH" => "ETHUSDT",
        "USDT-ERC20BTC" => "BTCUSDT",
        "USDT-ERC20ETH" => "ETHUSDT",
        "BTCETH" => "ETHBTC",
    ],

    'trading_view' => [
        'data_source' => 'BINANCE:',
        'widgets_src' => [
            'ticker_tape' => 'https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js',
            'symbol_info' => 'https://s3.tradingview.com/external-embedding/embed-widget-symbol-info.js',
            'market_news' => 'https://s3.tradingview.com/external-embedding/embed-widget-timeline.js',
            'tech_analysis' => 'https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js',
            'ta' => 'https://s3.tradingview.com/external-embedding/embed-widget-technical-analysis.js',
        ],
    ],

    'symbols_swiper' => [
        ['name' => 'bitcoin', 'symbol' => 'BTCUSDT', 'logo' => '/app_assets/images/coin/market-1.jpg'],
        ['name' => 'Binance', 'symbol' => 'BNBUSDT', 'logo' => '/app_assets/images/coin/market-3.jpg'],
        ['name' => 'ethereum', 'symbol' => 'ETHUSDT', 'logo' => '/app_assets/images/coin/market-2.jpg'],
    ],

    'validation' => [
        'image_max_size' => 1024 * 5,
    ]
];
