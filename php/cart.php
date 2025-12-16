<?php
session_start();
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// 商品データ（仮にID, 名前, 価格, 画像パスを配列にしておく）
$products = [
	1 => ["name" => "サムギョプサル弁当", "price" => 900, "img" => "../img/takeout/Gemini_Generated_Image_uov7puov7puov7pu-removebg-preview.png"],
	2 => ["name" => "ビビンバ丼", "price" => 900, "img" => "../img/takeout/bibimbap-4887417_1280-removebg-preview.png"],
	3 => ["name" => "特製キンパ", "price" => 800, "img" => "../img/takeout/menu6.png"],
	4 => ["name" => "サムゲタンスープ", "price" => 1000, "img" => "../img/takeout/menu3.png"],
	5 => ["name" => "チジミ", "price" => 700, "img" => "../img/takeout/menu5.png"],
	6 => ["name" => "ヤンニョムチキン", "price" => 600, "img" => "../img/takeout/menu2.png"],
];

// カートの中身取得
$cart = $_SESSION['cart'] ?? [];
$cart_count = count($cart);
$total = 0;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>本格韓国料理 ソダム - テイクアウト</title>
	<link rel="stylesheet" href="../css/common.css">
	<link rel="stylesheet" href="../css/takeout.css">
	<link rel="stylesheet" href="../css/cart.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
	<header>
		<a href="index.html"><img id="header_logo" src="../img/common/logo.png" alt="ロゴ"></a>
		<div id="header_icons">
			<a href="takeout.php" class="icon_btn cart_icon">
				<i class="fa-solid fa-bag-shopping"></i>
				<span id="cart_count"><?= $cart_count ?></span>
			</a>
		</div>
		<button id="hamburger">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<nav id="nav_list">
			<ul>
				<li><a href="">TOP</a></li>
				<li><a href="">メニュー</a></li>
				<li><a href="">店舗情報</a></li>
				<li><a href="">来店予約</a></li>
				<li><a href="">テイクアウト</a></li>
				<li><a href="">お問い合わせ</a></li>
			</ul>
		</nav>
	</header>
		<main>
		<div class="title_section">
			<div class="title_text">
				<h1><span>T</span>akeout<br></h1>
				<p>テイクアウト</p>
			</div>
			<div class="title_img">
			</div>
		</div>
		<div class="h1_wrapper">
			<h1>カート</h1>
		</div>
		<div class="cart_container_wrapper">
			<?php if (!$cart): ?>
				<p>カートに商品は入っていません。</p>
			<?php else: ?>
				<?php foreach ($cart as $id => $qty):
					$product = $products[$id];
					$subtotal = $product['price'] * $qty;
					$total += $subtotal;
				?>
					<div class="cart_item">
    <div class="ci_img">
        <img src="<?= $product['img'] ?>" alt="<?= $product['name'] ?>">
    </div>

    <div class="ci_name"><?= $product['name'] ?></div>

    <div class="ci_price">￥<?= number_format($product['price']) ?></div>

    <div class="ci_quantity">
        <form method="post" action="cart_update.php" class="update-form">
            <select name="quantity" class="item_quantity" data-id="<?= $id ?>">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $qty ? 'selected' : '' ?>>
                        <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>
            <input type="hidden" name="product_id" value="<?= $id ?>">
        </form>
    </div>
	<div class="bottom_area">
    	<div class="item_sum">￥<?= $subtotal ?></div>

    	<div class="ci_delete">
        	<form method="post" action="cart_update.php">
            <input type="hidden" name="remove_id" value="<?= $id ?>">
            <button type="button" class="delete_btn" data-id="<?= $id ?>">
				<svg viewBox="0 0 30 30" viewBox="0 0 30 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            	<path d="M9 3V4H4V6H5V20C5 21.1 5.9 22 7 22H17C18.1 22 19 21.1 19 20V6H20V4H15V3H9ZM7 6H17V20H7V6ZM9 8V18H11V8H9ZM13 8V18H15V8H13Z"/>
       		 	</svg>
			</button>
        	</form>
    	</div>
		</div>
	</div>

				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<?php if ($cart_count > 0): ?>
			<div class="all_sum">
				<h2>合計金額</h2>
				<p>￥<?= $total ?></p>
			</div>
			<div class="foot_nav">
				<div class="backto_menu">
					<button>
						<a href="takeout.php">
							<span>メニューに戻る</span>
						</a>
					</button>
					</div>
					<div class="to_buy">
					<button>
						<a href="order_input.php">
							<span>購入画面へ</span>
						</a>
					</button>
					</div>
				</div>
		<?php else: ?>
		<?php endif; ?>

	</main>
	<footer>
		<div id="footer_inner">
			<div id="footer_main">
				<div id="footer_info">
					<a href="index.html"><img id="footer_logo" src="../img/common/logo.png" alt="ロゴ"></a>
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
						<li><a href="">来店予約</a></li>
						<li><a href="">テイクアウト</a></li>
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
	<script src="../js/takeout.js"></script>
	<script src="../js/cart.js"></script>
</body>

</html>