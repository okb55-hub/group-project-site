<?php
session_start();

if (empty($_SESSION['order'])) {
	header('Location: cart.php');
	exit;
}

$order = $_SESSION['order'];

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>決済方法選択 - 本格韓国料理 ソダム</title>
	<link rel="stylesheet" href="../css/common.css">
	<link rel="stylesheet" href="../css/payment.css">
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
		<a href="index.html"><img id="header_logo" src="../img/common/logo.png" alt="ロゴ"></a>
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
		<div class="title_section">
			<div class="title_text">
				<h1><span>O</span>rder</h1>
				<p>ご注文情報入力</p>
			</div>
		</div>

		<div class="payment_container">
			<h1>決済方法を選択してください</h1>

			<div class="order_summary">
				<h2>ご注文内容</h2>
				<p class="order_id">注文番号：<?= htmlspecialchars($order['order_id']) ?></p>
				<div class="summary_items">
					<?php foreach ($order['items'] as $item): ?>
						<div class="summary_item">
							<span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
							<span>￥<?= number_format($item['subtotal']) ?></span>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="summary_total">
					<strong>合計金額</strong>
					<strong class="total_price">￥<?= number_format($order['total']) ?></strong>
				</div>
			</div>

			<div class="payment_methods">
				<a href="stripe_checkout.php" class="payment_method_card">
					<div class="payment_icon">
						<img src="" alt="クレジットカード">
					</div>
					<div class="payment_info">
						<h3>クレジットカード決済</h3>
						<p>Visa / Mastercard / JCB / American Express</p>
					</div>
					<i class="fa-solid fa-chevron-right"></i>
				</a>

				<a href="payment_paypay.php" class="payment_method_card paypay">
					<div class="payment_icon">
						<img src="" alt="PayPay">
					</div>
					<div class="payment_info">
						<h3>PayPay決済</h3>
						<p>スマートフォンで簡単決済</p>
					</div>
					<i class="fa-solid fa-chevron-right"></i>
				</a>
			</div>

			<a href="order_input.php" class="back_link">
				<i class="fa-solid fa-arrow-left"></i> 注文内容を修正する
			</a>
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