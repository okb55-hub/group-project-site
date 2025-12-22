<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

// --- セッションチェック ---
if (!isset($_SESSION['reservation'])) {
    header('Location: reserve.php');
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$res = $_SESSION['reservation'];
$user_id     = $_SESSION['user_id'];
$reserve_date = $res['reserve_date'];
$num_people   = $res['num_people'];
$slot_id      = $res['slot_id'];
$seat_type    = $res['seat_type'];

// 初期化 (エラーメッセージ用)
$error = '';
$slot = null;
$user = null;

try {
    $db = getDb();

    // --- 1. 時間帯の取得 ---
    $stmt = $db->prepare("SELECT slot_time FROM time_slots WHERE slot_id = ?");
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$slot) {
        // 不正な slot_id
        $_SESSION['error_message'] = "選択された予約時間帯は無効です。";
        header('Location: reserve.php');
        exit;
    }

    $slot_time = $slot['slot_time'];

    // --- 2. 現在の予約状況を再チェック ---
    $stmt = $db->prepare("
    SELECT SUM(num_people) 
    FROM reservations 
    WHERE reserve_date = :date AND seat_type = :type AND slot_id = :slot
");
    $stmt->execute([
        'date' => $reserve_date,
        'type' => $seat_type,
        'slot' => $slot_id
    ]);
    $already_reserved = (int)$stmt->fetchColumn();

    // 席ごとの上限数を取得
    $stmt = $db->prepare("
    SELECT max_counter, max_table, max_zashiki
    FROM time_slots
    WHERE slot_id = :slot_id
");
    $stmt->execute(['slot_id' => $slot_id]);
    $capacity = $stmt->fetch(PDO::FETCH_ASSOC);

    // 席タイプ別の上限を選択
    $max_seat = [
        'counter' => $capacity['max_counter'],
        'table'   => $capacity['max_table'],
        'zashiki' => $capacity['max_zashiki']
    ][$seat_type];

    $remaining = $max_seat - $already_reserved;

    // 残席チェック
    if ($remaining < $num_people) {
        // 他に予約が入り満席になった場合
        $_SESSION['error_message'] = "申し訳ありません。選択された席は満席になりました。";
        header('Location: reserve.php');
        exit;
    }

    // --- 3. ログイン中のユーザー情報 ---
    $stmt = $db->prepare("SELECT name, email, tel FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //  ユーザー情報が存在しない場合（データ不整合など）
    if (!$user) {
        error_log("データ不整合エラー [reserve_confirm.php]: user_id ({$user_id}) がDBに見つかりません。");
        $_SESSION['error_message'] = "お客様の登録情報に問題があります。";
        header('Location: reserve.php');
        exit;
    }

    // 表示用の日付整形
    $formatted_date = date("Y年n月j日", strtotime($reserve_date));
    $formatted_time = substr($slot_time, 0, 5); // HH:MM

} catch (Exception $e) {
    // ログ記録
    if ($e instanceof PDOException) {
        error_log("DB接続エラー [reserve_confirm.php]: " . $e->getMessage());
    } else {
        error_log("その他のエラー [reserve_confirm.php]: " . $e->getMessage());
    }

    // エラーメッセージをセッションに格納し、リダイレクト
    $_SESSION['error_message'] = "システムエラーが発生しました。時間をおいて再度お試しください。";
    header('Location: reserve.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ご予約内容の最終確認ページです。日時、人数、お席のタイプに間違いがないかご確認の上、予約を確定してください。韓国料理ソダムが、皆様のご来店を心よりお待ちしております。">
    <meta name="robots" content="noindex, follow">
    <title>予約内容確認 - 本格韓国料理 ソダム</title>
    <link rel="icon" href="../favicon_reserve.ico">
    <link rel="stylesheet" href="../css/reserve_common.css">
    <link rel="stylesheet" href="../css/reserve_confirm.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@400;500;600;700&display=swap"
		rel="stylesheet">
</head>

<body>
    <?php
    require_once __DIR__ . "/reserve_logoheader.php";
    ?>

    <main>
        <div class="confirm-container">
            <h1>予約内容の確認</h1>
            <?php if (isset($_SESSION['error_message'])): ?>
                <p class="error_message">
                    <?= e($_SESSION['error_message']) ?>
                </p>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <section class="confirm_block">
                <h2>ご予約内容</h2>
                <div class="confirm_detail">
                    <div>
                        <p>日付：<?= e($formatted_date) ?></p>
                        <p>時間：<?= e($formatted_time) ?></p>
                        <p>人数：<?= e($num_people) ?> 名</p>
                        <p>席種：
                            <?=
                            $seat_type === 'counter' ? 'カウンター' : ($seat_type === 'table' ? 'テーブル' : '座敷')
                            ?>
                        </p>
                    </div>
                </div>
            </section>

            <section class="confirm_block">
                <h2>お客様情報</h2>
                <div class="confirm_detail">
                    <div>
                        <p>氏名：<?= e($user['name']) ?></p>
                        <p>メール：<?= e($user['email']) ?></p>
                        <p>電話番号：<?= e($user['tel']) ?></p>
                    </div>
                </div>
            </section>

            <form action="reserve_done.php" method="POST">
                <button type="submit" class="confirm-btn">予約を確定する</button>
            </form>

            <div class="back-link">
                <a href="reserve.php?return=1">← 予約画面に戻る</a>
            </div>
        </div>
    </main>
    <?php
    require_once __DIR__ . "/reserve_footer.php";
    ?>
</body>

</html>