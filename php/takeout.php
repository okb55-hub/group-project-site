<?php
session_start();

// カートに入っている商品の個数を数える（バッジ表示用） 
// $cart_count = 0; if (!empty($_SESSION['cart'])) { $cart_count = count($_SESSION['cart']); }


// Ajax処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    header("Content-Type: application/json");
    
    $id = intval($_POST['product_id']);
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // 種類ごとに数量1で追加（すでにある場合は数量を増やさない）
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 1;
    }
    
    // 種類数（カートバッジ用）
    $cart_count = count($_SESSION['cart']);
    
    echo json_encode([
        "status" => "ok",
        "cart_count" => $cart_count
    ]);
    exit;
}

// 通常のページ表示用（カート数取得）
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>本格韓国料理 ソダム - テイクアウト</title>
	<link rel="stylesheet" href="../css/common.css">
	<link rel="stylesheet" href="../css/takeout.css">
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
		<a href="../html/index.html id="header_logo" src="../img/common/logo.png" alt="ロゴ"></a>
		<div id="header_icons">

			<a href="cart.php" class="icon_btn cart_icon">
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
		<div class="title_section">
			<div class="title_text">
				<h1><span>T</span>akeout<br></h1>
				<p>テイクアウト</p>
			</div>
			<div class="title_img">
			</div>
		</div>
		<div class="main_section">
			<div class="main_text">
				<p>当店では本格韓国料理のテイクアウトを承っております。<br>サムギョプサルやチーズタッカルビ、石焼ビビンバなど人気メニューをご家庭やオフィスでもお楽しみいただけます。<br>ご予約はこちらのページからお願いします。
				</p>
			</div>
			<div class="order_notice">
    		<h3>ご注文時の注意事項</h3>
    		<ul>
       			<li>ご注文後、約15〜20分でお受け取りいただけます。</li>
       			<li>混雑状況によりお渡し時間が前後する場合がございます。</li>
        		<li>アレルギー対応は致しかねます。店内で複数の食材を扱っています。</li>
        		<li>商品の特性上、お受け取り後の返品・交換はできません。</li>
        		<li>受け取り時間の変更やキャンセルはお電話にてご連絡ください。</li>
   			 </ul>
			</div>
			<div class="h1_wrapper">
				<h1>メニュー</h1>
			</div>
			<div class="takeout_container">
				<div class="takeout_item" data-id="1">
					<img src="../img/takeout/Gemini_Generated_Image_uov7puov7puov7pu-removebg-preview.png" alt="">
					<div class="takeout_item_text">
						<p>サムギョプサル弁当</p>
						<p>￥900</p>
					</div>
					<button class="open-modal" data-target="modal-sam">詳細</button>
				</div>
				<div class="takeout_item" data-id="2">
					<img src="../img/takeout/bibimbap-4887417_1280-removebg-preview.png" alt="">
					<div class="takeout_item_text">
						<p>ビビンバ丼</p>
						<p>￥900</p>
					</div>
					<button class="open-modal" data-target="modal-bibin">詳細</button>
				</div>
				<div class="takeout_item" data-id="3">
					<img src="../img/takeout/menu6.png" alt="">
					<div class="takeout_item_text">
						<p>特製キンパ</p>
						<p>￥800</p>
					</div>
					<button class="open-modal" data-target="modal-kimpa">詳細</button>
				</div>
				<div class="takeout_item" data-id="4">
					<img src="../img/takeout/menu3.png" alt="">
					<div class="takeout_item_text">
						<p>サムゲタンスープ</p>
						<p>￥1000</p>
					</div>
					<button class="open-modal" data-target="modal-samgetan">詳細</button>
				</div>
				<div class="takeout_item" data-id="5">
					<img src="../img/takeout/menu5.png" alt="">
					<div class="takeout_item_text">
						<p>チヂミ</p>
						<p>￥700</p>
					</div>
					<button class="open-modal" data-target="modal-chijimi">詳細</button>
				</div>
				<div class="takeout_item" data-id="6">
					<img src="../img/takeout/menu2.png" alt="">
					<div class="takeout_item_text">
						<p>ヤンニョムチキン</p>
						<p>￥600</p>
					</div>
					<button class="open-modal" data-target="modal-yang">詳細</button>
				</div>
				<!-- モーダル表示部分 -->
				<!-- サムギョプサル弁当 -->
				<div id="modal-sam" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-content-container">
							<img src="../img/takeout/Gemini_Generated_Image_uov7puov7puov7pu-removebg-preview.png"
								alt="">
							<div class="modal-content-container-info">
								<h2>サムギョプサル弁当</h2>
								<p>本場韓国で親しまれるサムギョプサルを、香ばしくジューシーに焼き上げました。野菜と一緒に味わうことで、肉本来の旨みがより引き立ちます。お店の味をそのまま、贅沢に楽しめる一品です。
								</p>
        						<button type="button" class="cart-button" data-id="1" data-qty="1">
            						カートに入れる <i class="fa-solid fa-cart-arrow-down"></i>
        						</button>
							</div>
						</div>
					</div>
				</div>

				<!-- ビビンバ丼 -->
				<div id="modal-bibin" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-content-container">
							<img src="../img/takeout/bibimbap-4887417_1280-removebg-preview.png" alt="">
							<div class="modal-content-container-info">
								<h2>ビビンバ丼</h2>
								<p>彩り豊かなナムルと特製コチュジャンをたっぷりと。素材の風味を生かしながら丁寧に仕上げた、体にも優しい本場の味です。混ぜるほどに深まる旨みをご堪能ください。</p>
								<button type="button" class="cart-button" data-id="2" data-qty="1">
            						カートに入れる <i class="fa-solid fa-cart-arrow-down"></i>
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- 特製キンパ -->
				<div id="modal-kimpa" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-content-container">
							<img src="../img/takeout/menu6.png" alt="">
							<div class="modal-content-container-info">
								<h2>特製キンパ</h2>
								<p>毎日丁寧に巻き上げる、ソダム自慢の手作りキンパ。具材のバランスにこだわり、どこを食べても美味しさが続くよう仕上げました。お子様から大人まで楽しめる、人気の定番メニューです。
								</p>
								<button type="button" class="cart-button" data-id="3" data-qty="1">
            						カートに入れる <i class="fa-solid fa-cart-arrow-down"></i>
        						</button>
							</div>
						</div>
					</div>
				</div>

				<!-- サムゲタンスープ -->
				<div id="modal-samgetan" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-content-container">
							<img src="../img/takeout/menu3.png" alt="">
							<div class="modal-content-container-info">
								<h2>サムゲタンスープ</h2>
								<p>韓国伝統の滋味深いスープを、食べやすく優しい味わいに。鶏の旨みと漢方食材の香りがじんわりと広がり、心も体も温まります。疲れた日にもそっと寄り添う、癒しの一杯です。</p>
        						<button type="button" class="cart-button" data-id="4" data-qty="1">
            						カートに入れる <i class="fa-solid fa-cart-arrow-down"></i>
    							</button>
							</div>
						</div>
					</div>
				</div>

				<!-- チヂミ -->
				<div id="modal-chijimi" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-content-container">
							<img src="../img/takeout/menu5.png" alt="">
							<div class="modal-content-container-info">
								<h2>チヂミ</h2>
								<p>外はカリッと、中はふわり。素材の香りを引き立てる自家製の生地で焼き上げた、風味豊かな一枚です。特製のタレが、さらに旨みを深めてくれます。</p>
        						<button type="button" class="cart-button" data-id="5" data-qty="1">
            						カートに入れる <i class="fa-solid fa-cart-arrow-down"></i>
        						</button>
							</div>
						</div>
					</div>
				</div>

				<!-- ヤンニョムチキン -->
				<div id="modal-yang" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<div class="modal-content-container">
							<img src="../img/takeout/menu2.png" alt="">
							<div class="modal-content-container-info">
								<h2>ヤンニョムチキン</h2>
								<p>甘辛い特製ヤンニョムソースを絡めた、やみつき必至の人気メニュー。カリッと揚げたチキンに、コクと旨みがしっかり染みわたります。ひと口食べれば止まらなくなる、本場屋台の味です。
								</p>
								<button type="button" class="cart-button" data-id="6" data-qty="1">
            						カートに入れる <i class="fa-solid fa-cart-arrow-down"></i>
        						</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="foot_contain">
				<div class="to_cartbtn">
					<button>
						<a href="cart.php">
							<span>カートを確認する</span>
						</a>
					</button>
				</div>
		</div>
	</main>
	<footer>
		<div id="footer_inner">
			<div id="footer_main">
				<div id="footer_info">
					<a href="../html/index.html"><img id="footer_logo" src="../img/common/logo_white.png" alt="ロゴ"></a>
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
	<script>
    const cartItems = <?= json_encode(array_keys($_SESSION['cart'] ?? [])); ?>;
    </script>
	<script src="../js/hamburger.js"></script>
	<script src="../js/takeout.js"></script>
	<script src="../js/cart_badge.js"></script>
</body>

</html>