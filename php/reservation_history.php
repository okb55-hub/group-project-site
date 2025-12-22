<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

// ログインしていなければログインページへ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 変数の初期値設定
$user_id = $_SESSION['user_id'] ?? null;
$error = '';
$future_reservations = [];
$past_reservations = [];
$seat_label = [];
$display_name = "ゲスト";
$is_logged_in = false;
$is_Error = false;
$reserve_count = 0;

try {
    $db = getDb();

    if ($user_id > 0) {
        // ユーザーIDがある場合、DBから名前を取得
        $user_stmt = $db->prepare("SELECT name FROM users WHERE user_id = :user_id");
        $user_stmt->execute(['user_id' => $user_id]);
        $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $display_name = $user_data['name'];
            $is_logged_in = true;

            $count_sql = "SELECT COUNT(*) FROM reservations WHERE user_id = :uid AND reserve_date >= CURDATE()";
            $count_stmt = $db->prepare($count_sql);
            $count_stmt->execute(['uid' => $user_id]);
            $reserve_count = (int)$count_stmt->fetchColumn();
        }
    }

    // ▼ 予約履歴を取得（未来・過去すべて）
    $sql = "
        SELECT r.*, t.slot_time
        FROM reservations r
        LEFT JOIN time_slots t ON r.slot_id = t.slot_id
        WHERE r.user_id = ?
        ORDER BY r.reserve_date , t.slot_time ASC
    ";

    $stt = $db->prepare($sql);
    $stt->execute([$user_id]);
    $reservations = $stt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($reservations)) {
        // ▼ ステータス判定（過去 or 未来）
        $today = date("Y-m-d");
        foreach ($reservations as $r) {
            if ($r['reserve_date'] < $today) {
                // 過去の予約
                $r['status_text'] = "来店済み";
                $r['status_class'] = "done";
                $past_reservations[] = $r;
            } else {
                // 今後の予約（今日、未来）
                if ($r['reserve_date'] === $today) {
                    $r['status_text'] = "本日予約";
                    $r['status_class'] = "today";
                } else {
                    $r['status_text'] = "予約済み";
                    $r['status_class'] = "reserved";
                }
                $future_reservations[] = $r;
            }
        }

        // ▼ seat_type の日本語変換
        $seat_label = [
            'counter' => 'カウンター',
            'table'   => 'テーブル',
            'zashiki' => '座敷',
        ];
    }
} catch (Exception $e) {
    $error = "エラーが発生しました。<br>時間をおいて再度お試しください。";

    if ($e instanceof PDOException) {
        error_log("DB接続エラー [reservation_history.php]: " . $e->getMessage());
    } else {
        error_log("その他のエラー [reservation_history.php]: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="本格韓国料理ソダムのお客様の予約履歴確認ページです。ご予約状況や過去の来店履歴の確認が行えます。">
    <title>予約履歴 - 本格韓国料理 ソダム</title>
    <link rel="icon" href="../favicon_reserve.ico">
    <link rel="stylesheet" href="../css/reserve_common.css">
    <link rel="stylesheet" href="../css/reservation_history.css">
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
    <main>
        <div class="reservation_history_container">
            <h1>予約履歴</h1>

            <?php if ($error): ?>
                <p class="error_message"><?= $error ?></p>
            <?php endif; ?>

            <?php if (!$error): ?>
                <section class="info_block reservation_history">
                    <h2>今後の予約</h2>

                    <?php if (empty($future_reservations)): ?>
                        <p class="no_history">今後の予約はありません。</p>
                    <?php else: ?>
                        <p class="reservation_summary">
                            現在、<span class="reserve-count"><?= $reserve_count ?>件</span>のご予約を承っております。<br>
                            当日のご来店をスタッフ一同心よりお待ちしております。
                        </p>
                        <div class="table_wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>日付</th>
                                        <th>時間</th>
                                        <th>人数</th>
                                        <th>席タイプ</th>
                                        <th>ステータス</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($future_reservations as $r): ?>
                                        <tr class="future">
                                            <td data-label="日付"><?= date('Y年n月j日', strtotime($r['reserve_date'])) ?></td>
                                            <td data-label="時間"><?= e(substr($r['slot_time'], 0, 5)) ?></td>
                                            <td data-label="人数"><?= e($r['num_people']) ?>名</td>
                                            <td data-label="席タイプ"><?= e($seat_label[$r['seat_type']]) ?></td>
                                            <td data-label="ステータス">
                                                <span class="status_badge <?= e($r['status_class']) ?>">
                                                    <?= e($r['status_text']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="info_block reservation_history">
                    <h2>来店履歴</h2>

                    <?php if (empty($past_reservations)): ?>
                        <p class="no_history">来店履歴はありません。</p>
                    <?php else: ?>
                        <div class="table_wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>日付</th>
                                        <th>時間</th>
                                        <th>人数</th>
                                        <th>席タイプ</th>
                                        <th>ステータス</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($past_reservations as $r): ?>
                                        <tr class="past">
                                            <td data-label="日付"><?= date('Y年n月j日', strtotime($r['reserve_date'])) ?></td>
                                            <td data-label="時間"><?= e(substr($r['slot_time'], 0, 5)) ?></td>
                                            <td data-label="人数"><?= e($r['num_people']) ?>名</td>
                                            <td data-label="席タイプ"><?= e($seat_label[$r['seat_type']]) ?></td>
                                            <td data-label="ステータス">
                                                <span class="status_badge <?= e($r['status_class']) ?>">
                                                    <?= e($r['status_text']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <div class="reservepage_area">
                <a href="reserve.php" class="btn reservepage_btn">新規予約・空席確認へ進む</a>
            </div>

            <div class="back_link">
                <a href="mypage.php">← マイページへ</a>
            </div>

            
        </div>
    </main>
    <?php
    require_once __DIR__ . "/reserve_footer.php";
    ?>
    <script src="../js/reserve_common.js"></script>
</body>

</html>