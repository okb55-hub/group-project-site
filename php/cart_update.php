<?php
session_start();

header("Content-Type: application/json");

$products = [
    1 => ["name" => "サムギョプサル弁当", "price" => 900, "img" => "../img/takeout/Gemini_Generated_Image_uov7puov7puov7pu-removebg-preview.png"],
    2 => ["name" => "ビビンバ丼", "price" => 900, "img" => "../img/takeout/bibimbap-4887417_1280-removebg-preview.png"],
    3 => ["name" => "特製キンパ", "price" => 800, "img" => "../img/takeout/menu6.png"],
    4 => ["name" => "サムゲタンスープ", "price" => 1000, "img" => "../img/takeout/menu3.png"],
    5 => ["name" => "チジミ", "price" => 700, "img" => "../img/takeout/menu5.png"],
    6 => ["name" => "ヤンニョムチキン", "price" => 600, "img" => "../img/takeout/alaundra-alford-BmRbJBoudDw-unsplash-removebg-preview.png"],
];

// 合計金額を計算
function calculateTotal($cart, $products) {
    $total = 0;
    foreach ($cart as $id => $qty) {
        if (isset($products[$id])) {
            $total += $products[$id]['price'] * $qty;
        }
    }
    return $total;
}

// 数量更新
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $id = intval($_POST['product_id']);
    $qty = intval($_POST['quantity']);

    if ($qty > 0) {
        $_SESSION['cart'][$id] = $qty;
    }

    $cart_count = count($_SESSION['cart']);
    $total = calculateTotal($_SESSION['cart'], $products);
    $subtotal = $products[$id]['price'] * $qty;

    echo json_encode([
        "status" => "ok",
        "cart_count" => $cart_count,
        "total" => $total,
        "total_items" => array_sum($_SESSION['cart']),
        "subtotal" => $subtotal
    ]);
    exit;
}


// 削除
if (isset($_POST['remove_id'])) {
    $id = intval($_POST['remove_id']);
    unset($_SESSION['cart'][$id]);

    $cart_count = count($_SESSION['cart']);
    $total = calculateTotal($_SESSION['cart'], $products);

    echo json_encode([
        "status" => "ok",
        "cart_count" => $cart_count,
        "total" => $total,
        "total_items" => array_sum($_SESSION['cart'])
    ]);
    exit;
}

echo json_encode(["status" => "error"]);
exit;


