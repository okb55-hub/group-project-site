<?php
session_start();

require_once 'stripe_config.php';

if (empty($_SESSION['order'])) {
	header('Location: cart.php');
	exit;
}

$order = $_SESSION['order'];

try {
	// Checkoutセッションを作成
	$checkout_session = \Stripe\Checkout\Session::create([
		'payment_method_types' => ['card'],
		'line_items' => [
			[
				'price_data' => [
					'currency' => 'jpy',
					'product_data' => [
						'name' => '本格韓国料理 ソダム - テイクアウト注文',
						'description' => '注文番号：' . $order['order_id'],

					],
					'unit_amount' => $order['total'],
				],
				'quantity' => 1,
			],
		],
		'mode' => 'payment',
		'success_url' => 'http://localhost/group-project-site/php/order_complete.php?session_id={CHECKOUT_SESSION_ID}',
		'cancel_url' => 'http://localhost/group-project-site/php/payment_select.php',
		'customer_email' => $order['email'],
		'metadata' => [
			'order_id' => $order['order_id'],
			'customer_name' => $order['name'],
			'customer_tel' => $order['tel'],
			'pickup_date' => $order['pickup_date'],
			'pickup_time' => $order['pickup_time'],
		],
	]);

	// セッションIDを保存
	$_SESSION['stripe_session_id'] = $checkout_session->id;

	// Checkoutページにリダイレクト
	header('Location: ' . $checkout_session->url);
	exit;

} catch (Exception $e) {
	$_SESSION['payment_error'] = $e->getMessage();
	header('Location: payment_select.php');
	exit;
}
