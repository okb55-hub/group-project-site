<?php
session_start();

// POSTデータがない場合はエラー
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: cart.php');
	exit;
}

// 商品データ
$products = [
	1 => ["name" => "サムギョプサル弁当", "price" => 900, "img" => "../img/takeout/Gemini_Generated_Image_uov7puov7puov7pu-removebg-preview.png"],
	2 => ["name" => "ビビンバ丼", "price" => 900, "img" => "../img/takeout/bibimbap-4887417_1280-removebg-preview.png"],
	3 => ["name" => "特製キンパ", "price" => 800, "img" => "../img/takeout/menu6.png"],
	4 => ["name" => "サムゲタンスープ", "price" => 1000, "img" => "../img/takeout/menu3.png"],
	5 => ["name" => "チジミ", "price" => 700, "img" => "../img/takeout/menu5.png"],
	6 => ["name" => "ヤンニョムチキン", "price" => 600, "img" => "../img/takeout/alaundra-alford-BmRbJBoudDw-unsplash-removebg-preview.png"],
];

// フォームデータ取得
$name = trim($_POST['name'] ?? '');
$tel = trim($_POST['tel'] ?? '');
$email = trim($_POST['email'] ?? '');
$pickup_date = $_POST['pickup_date'] ?? '';
$pickup_time = $_POST['pickup_time'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// バリデーション
if (empty($name) || empty($tel) || empty($email) || empty($pickup_date) || empty($pickup_time) || empty($payment_method)) {
	header('Location: order_input.php');
	exit;
}

// 合計金額計算
$total = 0;
$order_items = [];

foreach ($_SESSION['cart'] as $id => $qty) {
	if (isset($products[$id])) {
		$product = $products[$id];
		$subtotal = $product['price'] * $qty;
		$total += $subtotal;

		$order_items[] = [
			'name' => $product['name'],
			'price' => $product['price'],
			'quantity' => $qty,
			'subtotal' => $subtotal
		];
	}
}

// 注文情報をセッションに保存
$_SESSION['order'] = [
	'name' => $name,
	'tel' => $tel,
	'email' => $email,
	'pickup_date' => $pickup_date,
	'pickup_time' => $pickup_time,
	'payment_method' => $payment_method,
	'items' => $order_items,
	'total' => $total,
	// 注文番号作成
	'order_id' => 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999),
	'created_at' => date('Y-m-d H:i:s')
];

// 決済方法によって分岐
if ($payment_method === 'store') {
	header('Location: order_complete.php');
	exit;
} else {
	header('Location: payment_select.php');
	exit;
}