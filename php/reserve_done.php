<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

// --- セッションチェック ---
if (!isset($_SESSION['reservation'], $_SESSION['user_id'])) {
    header('Location: reserve.php');
    exit;
}

$res = $_SESSION['reservation'];
$user_id     = $_SESSION['user_id'];
$reserve_id = null; // 予約IDを保存用

try {
    $db = getDb();
    $db->beginTransaction();

    $stmt = $db->prepare("
        INSERT INTO reservations
            (user_id, reserve_date, slot_id, seat_type, num_people, created_at)
        VALUES
            (:user, :date, :slot, :seat, :people, NOW())
    ");

    $stmt->execute([
        ':user'   => $user_id,
        ':date'   => $res['reserve_date'],
        ':slot'   => $res['slot_id'],
        ':seat'   => $res['seat_type'],
        ':people' => $res['num_people'],
    ]);

    // 今回INSERTした予約IDを取得
    $reserve_id = $db->lastInsertId();

    $db->commit();
unset($db);
    unset($_SESSION['reservation']);

} catch (Exception $e) {
    $db->rollBack();
     $_SESSION['error_message'] = "予約に失敗しました。もう一度お試しください。";
    header('Location: reserve_confirm.php');
    exit;
}

// --- DBから今予約した情報を取得（表示用） ---
$reservation = null;
try {
    $db = getDb();
    $stmt = $db->prepare("
        SELECT 
            r.reserve_id,
            r.reserve_date,
            r.num_people,
            r.seat_type,
            t.slot_time,
            u.name,
            u.email,
            u.tel
        FROM reservations r
        JOIN time_slots t ON r.slot_id = t.slot_id
        JOIN users u ON r.user_id = u.user_id
        WHERE r.reserve_id = ?
    ");
    $stmt->execute([$reserve_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reservation) {
        // 表示用に整形
        $formatted_date = date("Y年n月j日", strtotime($reservation['reserve_date']));
        $formatted_time = substr($reservation['slot_time'], 0, 5);
        $seat_type_jp = [
            'counter' => 'カウンター',
            'table' => 'テーブル',
            'zashiki' => '座敷'
        ][$reservation['seat_type']];
    }

    if (!$reservation) {
        $reservation = null;
    }

} catch (Exception $e) {
    $reservation = null;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予約完了</title>
    <link rel="stylesheet" href="../css/reserve_common.css">
    <link rel="stylesheet" href="./../css/reserve_done.css">
</head>
<body>

<main class="done-container">
    
    <!-- 成功メッセージ -->
    <div class="success-header">
        <div class="success-icon">✓</div>
        <h1>ご予約が完了しました</h1>
        <p class="success-message">ご来店を心よりお待ちしております</p>
    </div>

    <!-- 予約内容 -->
    <?php if ($reservation): ?>
    <section class="reservation-details">
        <h2>ご予約内容</h2>
        <div class="detail-item">
            <span class="label">日付</span>
            <span class="value"><?= e($formatted_date) ?></span>
        </div>
        <div class="detail-item">
            <span class="label">時間</span>
            <span class="value"><?= e($formatted_time) ?></span>
        </div>
        <div class="detail-item">
            <span class="label">人数</span>
            <span class="value"><?= e($reservation['num_people']) ?> 名</span>
        </div>
        <div class="detail-item">
            <span class="label">席種</span>
            <span class="value"><?= e($seat_type_jp) ?></span>
        </div>
    </section>

    <!-- お客様情報 -->
    <section class="customer-details">
        <h2>お客様情報</h2>
        <div class="detail-item">
            <span class="label">お名前</span>
            <span class="value"><?= e($reservation['name']) ?></span>
        </div>
        <div class="detail-item">
            <span class="label">メールアドレス</span>
            <span class="value"><?= e($reservation['email']) ?></span>
        </div>
        <div class="detail-item">
            <span class="label">電話番号</span>
            <span class="value"><?= e($reservation['tel']) ?></span>
        </div>
    </section>

    <?php else: ?>
    <section class="error-notice">
        <p class="error">
            ※予約は正常に完了していますが、詳細情報の取得に失敗しました。<br>
            お手数ですが、予約履歴より予約内容をご確認ください。
        </p>
    </section>
<?php endif; ?>

    <!-- 注意事項 -->
    <section class="notice-section">
        <h3>ご来店に際してのお願い</h3>
        <ul>
            <li>ご予約の時間に遅れる場合は、お電話にて直接店舗までご連絡をお願いいたします。</li>
            <li>予約内容の確認・キャンセルは予約履歴から可能です</li>
        </ul>
    </section>

    <!-- アクションボタン -->
    <div class="action-buttons">
        <a href="reservation_history.php" class="btn-primary">予約履歴を確認</a>
        <a href="reserve.php" class="btn-secondary">トップページへ</a>
    </div>

</main>

</body>
</html>
