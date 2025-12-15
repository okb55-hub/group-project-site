<?php
// Stripe設定ファイル

// Composerのオートローダーの読み込み
require_once __DIR__ . '/../vendor/autoload.php';

// Stripeのキー設定（pk_test_～：テストモード）
define('STRIPE_PUBLISHABLE_KEY', 'YOUR_STRIPE_PUBLIC_KEY');
define('STRIPE_SECRET_KEY', 'YOUR_STRIPE_SECRET_KEY');

// Stripe API初期化
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// 通貨設定
define('CURRENCY', 'jpy');

// テストモードかどうか
define('IS_TEST_MODE', true);
