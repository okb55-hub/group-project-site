<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

// --- セッションチェック ---
if (!isset($_SESSION['reservation'], $_SESSION['user_id'])) {
    header('Location: reserve.php');
    exit;
}

$res = $_SESSION['reservation'];
$reserve_id = null; // 予約IDを保存用
$user_id = $_SESSION['user_id'] ?? null;
$user = null;
$display_name = "ゲスト";
$is_logged_in = false;
$seat_type_jp = '';

try {
    $db = getDb();


		// 1．ユーザー情報の取得（ヘッダー・予約完了後のお客様情報用）
		$user_stmt = $db->prepare("SELECT name, email, tel FROM users WHERE user_id = :user_id");
		$user_stmt->execute(['user_id' => $user_id]);
		$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

		if ($user_data) {
			$display_name = $user_data['name'];
			$is_logged_in = true;
		}


    // 2.予約登録のための処理（トランザクション）
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

// 3．完了後の予約情報用のデータ取得
$stmt = $db->prepare("
        SELECT r.*, t.slot_time 
        FROM reservations r
        JOIN time_slots t ON r.slot_id = t.slot_id
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
     unset($_SESSION['reservation']);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("エラー [reserve_confirm.php]: " . $e->getMessage());
     $_SESSION['error_message'] = "予約に失敗しました。再度お試しいただくか、お電話での予約をお願いします。";
    header('Location: reserve.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予約完了</title>
    <link rel="stylesheet" href="../css/reserve_common.css">
    <link rel="stylesheet" href="./../css/reserve_done.css">

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
	require_once __DIR__ . "/reserve_header.php";
	?>

<main class="done-container">
    
    <!-- 成功メッセージ -->
    <div class="success-header">
        <div class="success-icon">✓</div>
        <h1>ご予約が完了しました</h1>
        <p class="success-message">ご来店を心よりお待ちしております</p>
    </div>

    <!-- 予約内容 -->
    <?php if ($reservation && $user_data): ?>
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
            <span class="value"><?= e($user_data['name']) ?></span>
        </div>
        <div class="detail-item">
            <span class="label">メールアドレス</span>
            <span class="value"><?= e($user_data['email']) ?></span>
        </div>
        <div class="detail-item">
            <span class="label">電話番号</span>
            <span class="value"><?= e($user_data['tel']) ?></span>
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
            <li>予約内容の確認は予約履歴から可能です</li>
        </ul>
    </section>

    <!-- アクションボタン -->
    <div class="action-buttons">
        <a href="reservation_history.php" class="btn-primary">予約履歴を確認</a>
        <a href="reserve.php" class="btn-secondary">トップページへ</a>
    </div>

</main>
<?php
	require_once __DIR__ . "/reserve_footer.php";
	?>
</body>
</html>
