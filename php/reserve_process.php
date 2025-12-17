<?php
require_once __DIR__ . "/init.php";
// POST・セッションのデータがなければreserve.phpにリダイレクト
if (!isset($_POST['slot_id'], $_POST['seat_type'])) {
    header('Location: reserve.php');
    exit;
}
if (!isset($_SESSION['search_date'], $_SESSION['search_people'])) {
    $_SESSION['error_message'] = 'セッションが切れました。もう一度最初から操作してください。';
    header('Location: reserve.php');
    exit;
}

// 値の取得
$slot_id = (int)$_POST['slot_id'];
$seat_type = $_POST['seat_type'];
$reserve_date = $_SESSION['search_date'];
$num_people = $_SESSION['search_people'];

// POST送信データの値のバリデーション
// slot_id
if ($slot_id <= 0) {
    $_SESSION['error_message'] = '不正な操作が行われました。';
    header('Location: reserve.php');
    exit;
}
// seat_type
if (!in_array($seat_type, ['counter', 'table', 'zashiki'])) {
    $_SESSION['error_message'] = '不正な席タイプが選択されました。';
    header('Location: reserve.php');
    exit;
}

// セッション保存
$_SESSION['reservation'] = [
    'reserve_date'      => $reserve_date,
    'num_people'    => $num_people,
    'slot_id'   => $slot_id,
    'seat_type' => $seat_type
];
// ログインチェック
if (isset($_SESSION['user_id'])) {
    header('Location: reserve_confirm.php');
} else {
    header('Location: login.php');
}
exit;