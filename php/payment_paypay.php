<?php
session_start();

if (empty($_SESSION['order'])) {
	header('Location: cart.php');
	exit;
}

$order = $_SESSION['order'];

$total = $order['total'];

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
	<link href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
	<header>
		<h1>PayPay（疑似）</h1>
	</header>

	<main>
		<div class="paypay_container">
			<h1>PayPay決済</h1>

			<div class="paypay_box">
				<img class="paypay_logo" src="" alt="PayPayのロゴ">
				 <p class="paypay_amount">
					お支払い金額<br>
					<span>¥<?= number_format($total) ?></span>
				</p>

				<button id="payButton" class="pay_btn">PayPayで支払う</button>
			</div>
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