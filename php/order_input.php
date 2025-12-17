<?php
session_start();

// 商品データ
$products = [
	1 => ["name" => "サムギョプサル弁当", "price" => 900, "img" => "../img/takeout/takeout_sambox.png"],
	2 => ["name" => "ビビンバ丼", "price" => 900, "img" => "../img/takeout/takeout_bibinbap.png"],
	3 => ["name" => "特製キンパ", "price" => 800, "img" => "../img/takeout/takeout_kimpa.png"],
	4 => ["name" => "サムゲタンスープ", "price" => 1000, "img" => "../img/takeout/takeout_samgyetang.png"],
	5 => ["name" => "チヂミ", "price" => 700, "img" => "../img/takeout/takeout_chidimi.png"],
	6 => ["name" => "ヤンニョムチキン", "price" => 600, "img" => "../img/takeout/takeout_yangnyeom.png"],
];

// カート情報
$cart = $_SESSION['cart'];
$cart_count = count($cart);
$total = 0;

// 合計金額計算
foreach ($cart as $id => $qty) {
	$total += $products[$id]['price'] * $qty;
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ご注文情報入力 - 本格韓国料理 ソダム</title>
	<link rel="stylesheet" href="../css/common.css">
	<link rel="stylesheet" href="../css/order.css">
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
		<a href="../html/index.html"><img id="header_logo" src="../img/common/logo.png" alt="ロゴ"></a>
		<button id="hamburger">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<nav id="nav_list">
			<ul>
				<li><a href="../html/index.html">TOP</a></li>
				<li><a href="../html/menu.html">メニュー</a></li>
				<li><a href="../html/shop.html">店舗情報</a></li>
				<li><a href="../php/reserve.php" target="_blank">来店予約</a></li>
				<li><a href="../php/takeout.php" target="_blank">テイクアウト</a></li>
				<li><a href="../html/contact.html">お問い合わせ</a></li>
				<li><a href="../html/policy.html">プライバシーポリシー</a></li>
			</ul>
		</nav>
	</header>

	<main>
		<div class="order_container">
			<h1>ご注文情報の入力</h1>
			<div id="error_messages" class="error_messages" style="display: none;"></div>

			<!-- お客様情報入力フォーム -->
			<div class="customer_info">
				<h2>お客様情報</h2>
				<form id="order_form">
					<div class="form_group">
						<label for="name">お名前</label>
						<input type="text" id="name" name="name" placeholder="山田 太郎" required>
					</div>

					<div class="form_group">
						<label for="tel">電話番号</label>
						<input type="tel" id="tel" name="tel" placeholder="000-1234-5678" required>
						<small class="form_note">ハイフン(-)を含めて入力してください</small>
					</div>

					<div class="form_group">
						<label for="email">メールアドレス</label>
						<input type="email" id="email" name="email" placeholder="user@example.jp" required>
						<small class="form_note">注文確認メールをお送りします</small>
					</div>

					<div class="form_group">
						<label for="picup_date">受け取り希望日</label>
						<input type="date" id="pickup_date" name="pickup_date" min="<?= date('Y-m-d') ?>" required>
					</div>

					<div class="form_group">
						<label for="pickup_time">受け取り希望時間</label>
						<select name="pickup_time" id="pickup_time" required>
							<option value="">選択してください</option>
							<?php
							for ($h = 17; $h <= 22; $h++) {
								foreach (['00', '30'] as $m) {
									$time = sprintf('%02d:%s', $h, $m);
									echo "<option value=\"{$time}\">{$time}</option>";
								}
							}
							?>
						</select>
						<small class="form_note">営業時間：17:00～24：00（定休日：水曜日）<br>
							営業時間外のテイクアウトについては、電話にてお問い合わせください。
						</small>
					</div>

					<div class="form_group">
						<label>決済方法</label>
						<div class="radio_group">
							<label class="radio_label">
								<input type="radio" name="payment_method" value="store">
								<span>店頭支払い</span>
							</label>
							<label class="radio_label">
								<input type="radio" name="payment_method" value="online">
								<span>事前決済（クレジットカード・PayPay）</span>
							</label>
						</div>
					</div>

					<div class="form_actions">
						<a href="cart.php" class="btn_secondary"><i class="fa-solid fa-arrow-left"></i>カートに戻る</a>
						<button type="button" id="confirm_btn" class="btn_primary">確認する <i class="fa-solid fa-arrow-right"></i></button>
					</div>
				</form>
			</div>
		</div>
	</main>

	<!-- 確認モーダル -->
	<div id="confirm_modal" class="modal_confirm">
		<div class="modal_confirm_content">
			<span class="modal_close">&times;</span>
			<div class="modal_scroll_area">
				<h2><i class="fa-solid fa-clipboard-check"></i> ご注文内容の確認</h2>

				<div class="modal_section">
					<h3>注文商品</h3>
					<div class="modal_items">
						<?php foreach ($cart as $id => $qty) :
							$product = $products[$id];
							$subtotal = $product['price'] * $qty;
						?>

							<div class="modal_item">
								<img src="<?= $product['img'] ?>" alt="<?= $product['name'] ?>">
								<div class="modal_item_details">
									<p class="modal_item_name"><?= $product['name'] ?></p>
									<p class="modal_item_price">￥<?= $product['price'] ?> × <?= $qty ?>個 = ￥<?= $subtotal ?></p>
								</div>
							</div>

						<?php endforeach; ?>
					</div>
					<div class="modal_total">
						<strong>合計金額：￥<?= $total ?></strong>
					</div>
				</div>

				<div class="modal_section">
					<h3>お客様情報</h3>
					<table class="modal_info_table">
						<tr>
							<th>お名前</th>
							<td id="confirm_name"></td>
						</tr>
						<tr>
							<th>電話番号</th>
							<td id="confirm_tel"></td>
						</tr>
						<tr>
							<th>メールアドレス</th>
							<td id="confirm_email"></td>
						</tr>
						<tr>
							<th>受け取り日時</th>
							<td id="confirm_datetime"></td>
						</tr>
						<tr>
							<th>決済方法</th>
							<td id="confirm_payment"></td>
						</tr>
					</table>
				</div>

				<div class="modal_actions">
					<button type="button" class="btn_secondary modal_edit_btn">修正する</button>
					<form id="order_submit_form" action="order_process.php" method="post">
						<input type="hidden" name="name" id="hidden_name">
						<input type="hidden" name="tel" id="hidden_tel">
						<input type="hidden" name="email" id="hidden_email">
						<input type="hidden" name="pickup_date" id="hidden_pickup_date">
						<input type="hidden" name="pickup_time" id="hidden_pickup_time">
						<input type="hidden" name="payment_method" id="hidden_payment_method">
						<button type="submit" class="btn_primary btn_submit">
							<span id="submit_text">注文を確定する</span>
							<i class="fa-solid fa-check"></i>
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<footer>
		<div id="footer_inner">
			<div id="footer_main">
				<div id="footer_info">
					<a href="../html/index.html"><img id="footer_logo" src="../img/common/logo.png" alt="ロゴ"></a>
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
						<li><a href="../html/index.html">TOP</a></li>
						<li><a href="../html/menu.html">メニュー</a></li>
						<li><a href="../html/shop.html">店舗情報</a></li>
						<li><a href="../php/reserve.php" target="_blank">来店予約</a></li>
						<li><a href="../php/takeout.php" target="_blank">テイクアウト</a></li>
						<li><a href="../html/contact.html">お問い合わせ</a></li>
						<li><a href="../html/policy.html">プライバシーポリシー</a></li>
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
	<script src="../js/order.js"></script>
</body>

</html>