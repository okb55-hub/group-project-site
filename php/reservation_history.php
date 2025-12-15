<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

// ログインしていなければログインページへ
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

// 変数の初期値設定
$user_id = $_SESSION['user_id'] ?? null;
$error = '';
$future_reservations = [];
$past_reservations = [];
$seat_label = [];
try {
    $db = getDb();

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
    <title>予約履歴</title>
    <link rel="stylesheet" href="../css/reserve_common.css">
    <link rel="stylesheet" href="../css/reservation_history.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="mypage_container">
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

        <div class="mypage_area">
            <a href="mypage.php" class="btn mypage_btn">マイページ</a>
        </div>

        <div class="back_link">
            <a href="reserve.php">← トップページへ戻る</a>
        </div>
    </div>

</body>

</html>