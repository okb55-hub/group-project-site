<?php
// Discord Webhook取得

define('DISCORD_WEBHOOK_URL', 'YOUR_DISCORD_WEBHOOK_URL');

/**
 * Discordに通知送信
 * 
 * @param array $order 注文情報
 * @return bool 成功でtrue
 */

function sendDiscordNotification($order) {
	$webhook_url = DISCORD_WEBHOOK_URL;

	if ($order['payment_method'] === 'store') {
	
		$payment_text = '店頭支払い';
		$color = 16776960;

	} elseif ($order['payment_method'] === 'stripe' || (isset($order['payment_type']) && $order['payment_type'] === 'stripe')) {
		
		// カード決済
		if (isset($order['payment_status']) && $order['payment_status'] === 'paid') {
			$payment_text = 'カード決済（決済完了）';
			$color = 65280;
		} else {
			$payment_text = 'カード決済（決済未）';
			$color = 3447003;
		}
	
	} elseif ($order['payment_method'] === 'paypay' || (isset($order['payment_type']) && $order['payment_type'] === 'paypay')) {
	
		// PayPay決済
		if (isset($order['payment_status']) && $order['payment_status'] === 'paid') {
			$payment_text = 'PayPay決済（決済完了）';
			$color = 16711680;
		} else {
			$payment_text = 'PayPay決済（決済未）';
			$color = 16711680;
		}
	
	} else {
	
		// 事前決済でタイプ不明な時
		if (isset($order['payment_status']) && $order['payment_status'] === 'paid') {
			$payment_text = '事前決済（決済完了）';
			$color = 65280;
		} else {
			$payment_text = '事前決済（決済未）';
			$color = 3447003;
		}
	
	}

	// 注文リスト作成
	$items_text = '';
	foreach ($order['items'] as $item) {
		$items_text .= "・{$item['name']} × {$item['quantity']}個\n";
		$items_text .= "  ￥" . number_format($item['subtotal']) . "\n";
	}

	// メッセージ
	$data = [
		'content' => '【新規注文】',
		'embeds' => [
			[
				'title' => '注文番号：' . $order['order_id'],
				'color' => $color,
				'fields' => [
					[
						'name' => '注文内容：',
						'value' => $items_text,
						'inline' => false
					],
					[
						'name' => '合計金額：',
						'value' => '￥' . number_format($order['total']),
						'inline' => true
					],
					[
						'name' => '決済方法：',
						'value' => $payment_text,
						'inline' => true
					],
					[
						'name' => 'お客様情報：',
						'value' => "  お名前：{$order['name']}\n
						  電話番号：{$order['tel']}\n
						  メール：{$order['email']}",
						'inline' => false
					],
					[
						'name' => '受け取り日時：',
						'value' => "{$order['pickup_date']} {$order['pickup_time']}",
						'inline' => false
					],
				],
				'footer' => [
					'text' => '本格韓国料理 ソダム - テイクアウト注文システム'
				],
				'timestamp' => date('c')
			]
		]
	];

	$json_data = json_encode($data);

	// cURLで送信
	$ch = curl_init($webhook_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($http_code >= 200 && $http_code < 300) {
		return true;
	} else {
		error_log('Discord通知送信失敗: HTTP ' . $http_code);
		return false;
	}
}



