<?php
session_start();

// Stripe設定読み込み
if (isset($_GET['session_id'])) {
	require_once 'stripe_config.php';

	try {
		// Checkoutセッション情報を取得
		$session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

		// 決済情報をセッションに保存
		if ($session->payment_status === 'paid') {
			$_SESSION['order']['payment_status'] = 'paid';
			$_SESSION['order']['payment_id'] = $session->payment_intent;
			$_SESSION['order']['payment_type'] = 'stripe';
			$_SESSION['order']['paid_at'] = date('Y-m-d H:i:s');
		}

	} catch(Exception $e) {
		error_log('Stripe session retrieval error: ' . $e->getMessage());
	}
}

// PayPay決済の場合
if (isset($_GET['paypay_success'])) {
	$_SESSION['order']['payment_status'] = 'paid';
	$_SESSION['order']['payment_type'] = 'paypay';
	$_SESSION['order']['paid_at'] = date('Y-m-d H:i:s');
}

// 注文情報がない場合はカートへ
if (empty($_SESSION['order'])) {
	header('Location: cart.php');
	exit;
}

$order = $_SESSION['order'];

// 決済方法の表示名
$payment_method_name = '';
if ($order['payment_method'] === 'store') {
	$payment_method_name = '店頭支払い';
} else {
	$payment_method_name = '事前決済（クレジットカード・PayPay）';
	
	if (isset($order['payment_status']) && $order['payment_status'] === 'paid') {
		$payment_method_name  .= '：決済完了';
	}
}

// メール送信（簡易版）
$to = $order['email'];
$subject = '【本格韓国料理 ソダム】ご注文を承りました';
$message = "
{$order['name']} 様

ご注文ありがとうございます。
以下の内容でご注文を承りました。

━━━━━━━━━━━━━━━━━━━━
注文番号: {$order['order_id']}
━━━━━━━━━━━━━━━━━━━━

【ご注文内容】
";

foreach ($order['items'] as $item) {
	$message .= "{$item['name']} × {$item['quantity']}個 - ￥" . number_format($item['subtotal']) . "\n";
}

$message .= "
合計金額：￥" . number_format($order['total']) . "

【お客様情報】
お名前: {$order['name']}
電話番号: {$order['tel']}
メールアドレス: {$order['email']}

【受け取り情報】
受け取り日時: {$order['pickup_date']} {$order['pickup_time']}
決済方法: {$payment_method_name}

━━━━━━━━━━━━━━━━━━━━

ご来店を心よりお待ちしております。

※このメールは自動送信です。ご不明な点がございましたらお電話にてお問い合わせください。

本格韓国料理 ソダム
〒000-0000
石川県金沢市〇〇町0-0-0
営業時間：17:00～24:00（23:00 L.O）
定休日：水曜日
Tel：000-000-0000
";


$headers = 
	"From: noreply@sodam-restaurant.com\r\n" .
	"Content-Type: text/plain; charset=UTF-8\r\n" .
	"Content-Transfer-Encoding: 8bit\r\n" . 
	"X-Mailer: PHP 8\r\n";


// メール送信ここまで（SendGridで時間あればつくる）
// mb_send_mail($to, $subject, $message,$headers);

// Discordで店側に通知を送る
require_once 'discord_notify.php';
$discord_result = sendDiscordNotification($order);

if (!$discord_result) {
	error_log('Discord通知の送信に失敗しました: 注文番号 ' . $order['order_id']);
}

// カートクリア
unset($_SESSION['cart']);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>注文完了 - 本格韓国料理 ソダム</title>
	<link rel="stylesheet" href="../css/common.css">
	<link rel="stylesheet" href="../css/complete.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
	<header>
		<a href="index.html"><img id="header_logo" src="../img/common/logo_white.png" alt="ロゴ"></a>
		<button id="hamburger">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<nav id="nav_list">
			<ul>
				<li><a href="index.html">TOP</a></li>
				<li><a href="menu.html">メニュー</a></li>
				<li><a href="shop.html">店舗情報</a></li>
				<li><a href="../php/reserve.php" target="_blank">来店予約</a></li>
				<li><a href="../php/takeout.php" target="_blank">テイクアウト</a></li>
				<li><a href="contact.html">お問い合わせ</a></li>
			</ul>
		</nav>
	</header>

	<main>
		<div class="complete_container">
			<div class="success_icon">
				<i class="fa-solid fa-circle-check"></i>
			</div>

			<h1>ご注文ありがとうございました。</h1>
			<p class="complete_message">
				ご注文を承りました。<br>
				ご登録のメールアドレスに確認メールをお送りしております。
			</p>

			<div class="order_details">
				<h2>ご注文内容</h2>

				<div class="detail_section">
					<div class="detail_row">
						<span class="detail_label">注文番号</span>
						<span class="detail_value order_id"><?= htmlspecialchars($order['order_id']) ?></span>
					</div>
				</div>

				<div class="detail_section">
					<h3>ご注文商品</h3>
					<?php foreach ($order['items'] as $item): ?>
						<div class="detail_row">
							<span class="detail_label"><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
							<span class="detail_value">￥<?= number_format($item['subtotal']) ?></span>
						</div>
					<?php endforeach; ?>
					<div class="detail_row total_row">
						<span><strong>合計金額</strong></span>
						<span class="total_price"><strong>￥<?= number_format($order['total']) ?></strong></span>
					</div>
				</div>

				<div class="detail_section">
					<h3>お客様情報</h3>
					<div class="detail_row">
						<span class="detail_label">お名前</span>
						<span class="detail_value"><?= htmlspecialchars($order['name']) ?></span>
					</div>
					<div class="detail_row">
						<span class="detail_label">電話番号</span>
						<span class="detail_value"><?= htmlspecialchars($order['tel']) ?></span>
					</div>
					<div class="detail_row">
						<span class="detail_label">メールアドレス</span>
						<span class="detail_value"><?= htmlspecialchars($order['email']) ?></span>
					</div>
				</div>

				<div class="detail_section">
					<h3>受け取り情報</h3>
					<div class="detail_row">
						<span class="detail_label">受け取り日時</span>
						<span class="detail_value"><?= htmlspecialchars($order['pickup_date']) ?> <?= htmlspecialchars($order['pickup_time']) ?></span>
					</div>
					<div class="detail_row">
						<span class="detail_label">決済方法</span>
						<span class="detail_value"><?= htmlspecialchars($payment_method_name) ?></span>
					</div>
				</div>
			</div>
			
			<div class="notice_box">
				<h3><i class="fa-solid fa-info-circle"></i> ご来店について</h3>
				<ul>
					<li>指定された日時にご来店ください</li>
					<li>店頭にて注文番号またはお名前をお伝えください</li>
					<li>ご不明な点がございましたら、お電話にてお問い合わせください</li>
				</ul>
			</div>

			<div class="action_buttons">
				<a href="../html/index.html" class="btn_primary">
					<i class="fa-solid fa-house"> トップページへ</i>
				</a>
				<a href="takeout.php" class="btn_primary">
					<i class="fa-solid fa-utensils"></i> 他の商品を見る
				</a>
			</div>
		
		</div>
	</main>

	<footer>
		<div id="footer_inner">
			<div id="footer_main">
				<div>
					<a href="index.html"><img id="footer_logo" src="../img/common/logo_white.png" alt="ロゴ"></a>
					<div id="address">
						<p>〒000-0000</p>
						<p>石川県金沢市〇〇町0-0-0</p>
						<p>営業時間：17:00～24:00（23:00 L.O）</p>
						<p>定休日：水曜日</p>
						<p>Tel：000-000-0000</p>
					</div>
				</div>
				<div id="footer_nav">
					<ul>
						<li><a href="index.html">TOP</a></li>
						<li><a href="menu.html">メニュー</a></li>
						<li><a href="information.html">店舗情報</a></li>
						<li><a href="../php/reserve.php" target="_blank">来店予約</a></li>
						<li><a href="../php/takeout.php" target="_blank">テイクアウト</a></li>
						<li><a href="contact.html">お問い合わせ</a></li>
						<li><a href="policy.html">プライバシーポリシー</a></li>
					</ul>
				</div>
			</div>
			<div id="footer_foot">
				<small id="copyright">
					Copyright &copy; 本格韓国料理 ソダム All Right Reserved.
				</small>
			</div>
		</div>
	</footer>
	<script src="../js/hamburger.js"></script>
</body>
</html>
