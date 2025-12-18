<?php
session_start();

if (empty($_SESSION['order'])) {
	header('Location: cart.php');
	exit;
}

$order = $_SESSION['order'];

$total = $order['total'];

$datetime = new DateTime();
$formatted_date = $datetime->format('Y年m月d日 H時i分s秒');

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PayPay決済（疑似）</title>
	<link rel="stylesheet" href="../css/paypay.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=TikTok+Sans:opsz,wght@12..36,300..900&display=swap" rel="stylesheet">
</head>
<body>
	<header>
		<h1>PayPay（疑似）</h1>
	</header>

	<main>
		<div class="paypay_container">
			<div class="paypay_box">
				<div class="shop_icon">
					<img src="../img/paymethod/pay_shop.png" alt="ショップのアイコン">
				</div>
				<p class="shop_name">本格韓国料理 ソダム テイクアウト予約・購入</p>
				<p class="datetime"><?= $formatted_date ?></p>
				<p class="paypay_amount">
					<span><?= number_format($total) ?></span> 円
				</p>

				<div id="payButton" class="pay_btn">
					<img src="../img/paymethod/paypay_complete.png" alt="PayPayで支払う">
				</div>
			</div>
			<p class="comment">※疑似決済です。実際に決済されることはありません。</p>			
		</div>

	</main>

	<footer>
		<div id="footer_inner">
			<small id="copyright">
					Copyright &copy; PayPat疑似決済 All Right Reserved.
			</small>
		</div>
	</footer>
	<script src="../js/paypay.js"></script>
</body>
</html>