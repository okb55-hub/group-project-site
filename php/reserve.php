<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

$display_name = "ゲスト";
$is_logged_in = false;
$is_Error = false;

// 席の記号を返す関数
function getSeatStatus($remaining, $num_people)
{
	if ($remaining == 0 || $remaining < $num_people) {
		return ['symbol' => '×', 'text' => '満席', 'class' => 'full'];
	} elseif ($remaining <= 2) {
		return ['symbol' => '△', 'text' => "残{$remaining}席", 'class' => 'few'];
	} else {
		return ['symbol' => '◯', 'text' => "残{$remaining}席", 'class' => 'available'];
	}
}


try {

	$db = getDb();

	if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
		// ユーザーIDがある場合、DBから名前を取得
		$user_stmt = $db->prepare("SELECT name FROM users WHERE user_id = :user_id");
		$user_stmt->execute(['user_id' => $_SESSION['user_id']]);
		$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

		if ($user_data) {
			$display_name = $user_data['name'];
			$is_logged_in = true;
		}
	}


	/* 空席確認のための処理 */
	// 明日の日付取得（input[date]のmin用）
	$tomorrow = date('Y-m-d', strtotime('+1 day'));

	// 時間枠取得
	$time_stmt = $db->query("SELECT * FROM time_slots ORDER BY slot_time ASC");
	$time_slots = $time_stmt->fetchAll(PDO::FETCH_ASSOC);


	// 変数の初期値設定
	$is_first_view = true;   // 初回かどうか
	$reserve_date = null;    // 選択された日付
	$num_people = 1;         // 選択された人数
	$is_wednesday = false;   // 水曜日かどうか

	if (!isset($_GET['return'])) {
		// 戻るボタンが押されたとき以外は日付と人数のセッション初期化
		unset($_SESSION['search_date'], $_SESSION['search_people']);
	}

	// 1．条件分岐
	//検索ボタンが押された場合
	if (isset($_POST['reserve_date']) && isset($_POST['num_people'])) {

		$is_first_view = false; // 初回ではない
		$reserve_date = $_POST['reserve_date'];
		$num_people = (int)$_POST['num_people'];

		$current_date = date('Y-m-d');
        if ($reserve_date <= $current_date) {
            $_SESSION['error_message'] = 'ご予約は明日以降の日付でお願いいたします。';
            // リダイレクトにより、以降の処理（DBアクセスやセッションへの不正保存）を中断
            header('Location: reserve.php');
            exit; 
        }

		// セッションへ保存
		$_SESSION['search_date'] = $reserve_date;
		$_SESSION['search_people'] = $num_people;
	} // 一度確認画面に飛ぶ→戻るボタンを押した場合
	elseif (isset($_SESSION['search_date']) && isset($_SESSION['search_people'])) {

		$is_first_view = false;
		$reserve_date = $_SESSION['search_date'];
		$num_people = (int)$_SESSION['search_people'];

		// 初回アクセス（POSTもセッションもない場合）
	} else {
		$is_first_view = true;
		$reserve_date = date('Y-m-d', strtotime('+1 day')); // 明日を仮設定
		$num_people = 1; // 仮人数
	}

	// 2.それぞれの席のすでに入っている予約を取得
	// カウンター席
	$stmt = $db->prepare("
        SELECT slot_id, SUM(num_people) as reserved
        FROM reservations
        WHERE reserve_date = :date AND seat_type = 'counter'
        GROUP BY slot_id
    ");
	$stmt->execute(['date' => $reserve_date]);
	$counter_reservations = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

	// テーブル席
	$stmt = $db->prepare("
        SELECT slot_id, SUM(num_people) as reserved
        FROM reservations
        WHERE reserve_date = :date AND seat_type = 'table'
        GROUP BY slot_id
    ");
	$stmt->execute(['date' => $reserve_date]);
	$table_reservations = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

	// 座敷
	$stmt = $db->prepare("
        SELECT slot_id, SUM(num_people) as reserved
        FROM reservations
        WHERE reserve_date = :date AND seat_type = 'zashiki'
        GROUP BY slot_id
    ");
	$stmt->execute(['date' => $reserve_date]);
	$zashiki_reservations = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

	// 残席を計算
	foreach ($time_slots as $index => $slot) {
		$slot_id = $slot['slot_id'];

		// カウンター
		$reserved = $counter_reservations[$slot_id] ?? 0;
		$time_slots[$index]['counter_remaining'] = $slot['max_counter'] - $reserved;

		// テーブル
		$reserved = $table_reservations[$slot_id] ?? 0;
		$time_slots[$index]['table_remaining'] = $slot['max_table'] - $reserved;

		// 座敷
		$reserved = $zashiki_reservations[$slot_id] ?? 0;
		$time_slots[$index]['zashiki_remaining'] = $slot['max_zashiki'] - $reserved;
	}



	// DateTimeオブジェクト作成
	$date = new DateTime($reserve_date);

	// 曜日番号（0:日〜6:土）を取得
	$w = (int)$date->format('w');
	// 曜日の日本語配列
	$week_days = ['日', '月', '火', '水', '木', '金', '土'];
	$week_str = $week_days[$w];
	// 画面表示用の日付文字列を作成：例「2025年10月10日（金）」
	$formatted_date = $date->format('Y年n月j日') . "({$week_str})";
	// 水曜日判定
	if ($w == 3) {
		$is_wednesday = true;
	} else {
		$is_wednesday = false;
	}
} catch (Exception $e) {

	if ($e instanceof PDOException) {
		error_log("DB接続エラー [reserve.php]: " . $e->getMessage());
	} else {
		error_log("その他のエラー [reserve.php]: " . $e->getMessage());
	}
	$is_Error = true;
	$time_slots = [];
	$formatted_date = date('Y年n月j日', strtotime('+1 day')) . "（-）";
	$num_people = 1;
	$is_first_view = false;
	$is_wednesday = false;
}



?>

<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>来店予約 - 本格韓国料理 ソダム</title>
	<link rel="stylesheet" href="./../css/reserve_common.css">
	<link rel="stylesheet" href="./../css/reserve.css">

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
		<div class="main_section">
			<div class="menu_top">
				<div class="menu_top_text">
					<h2>ご来店予約について</h2>
					<p>当店では、ネット予約を24時間受け付けております。 <br>
						ご希望の日付・人数・時間をご入力のうえ、送信してください。<br>
						※団体（16名以上）のご予約はお電話のみ承っております。<br>
						※当日のご予約・人数変更はお電話にてお願いいたします。<br>
						<span class="tel_number">☎ 00-0000-0000</span>
					</p>
				</div>

				<div class="reserve_form">
					<?php if (isset($_SESSION['error_message'])): ?>
						<p class="error_message">
							<?= e($_SESSION['error_message']) ?>
						</p>
						<?php unset($_SESSION['error_message']); ?>
					<?php endif; ?>
					<form action="reserve.php" method="post">
						<div class="date_num_form">
							<div class="form_item">
								<label for="reserve_date">来店日</label>
								<!-- 一旦日付のminを消す -->
								<input type="date" name="reserve_date" id="reserve_date"  value="<?= htmlspecialchars($reserve_date) ?>">

							</div>
							<div class="form_item">
								<label for="num_people">人数</label>
								<select name="num_people" id="num_people">
									<?php for ($i = 1; $i <= 15; $i++): ?>
										<option value="<?= $i ?>" <?= $i == $num_people ? 'selected' : '' ?>><?= $i ?>名様</option>
									<?php endfor; ?>
								</select>
							</div>
							<div class="form_submit">
								<input type="submit" value="検索">
							</div>
						</div>

					</form>

				</div>
			</div>

			<div class="table_container">
				<!-- オーバーレイ表示 -->
				<?php if ($is_Error): ?><!-- DB接続エラーの際のオーバーレイ -->
					<div class="initial-overlay error-overlay">
						<p class="overlay-text">
							システムエラーが発生しているため、空席状況の取得に失敗しました。<br>しばらくお待ちいただくか、お電話にてご予約ください。
						</p>
					</div>
				<?php elseif (!$is_Error && $is_first_view): ?><!-- 初回表示の際のオーバーレイ -->
					<div class="initial-overlay">
						<p class="overlay-text">まずは来店希望日と人数をお選びください</p>
					</div>
				<?php elseif (!$is_Error && !$is_first_view && $is_wednesday): ?><!-- 水曜日の際のオーバーレイ -->
					<div class="initial-overlay">
						<p class="overlay-text">水曜日は定休日です。日付を変更して再度検索してください。</p>
					</div>
				<?php endif; ?>

				<h2><?= $formatted_date ?> の空席状況 <br class="sp_br"><span class="h2_num_people">（<?= $num_people ?>名様）</span></h2>
				<!-- 表の表示 -->
				<div class="table_wrapper">
					<table>
						<tr>
							<th class="seat-type"></th>
							<?php foreach ($time_slots as $slot): ?>
								<th><?= substr($slot["slot_time"], 0, 5) ?></th>
							<?php endforeach; ?>
						</tr>

						<!-- カウンター席 -->
						<tr>
							<th class="seat-type"> <img src="../img/reserve/counter.jpg" alt="カウンター席">カウンター</th>
							<?php foreach ($time_slots as $slot): ?>
								<?php $status = getSeatStatus($slot['counter_remaining'], $num_people); ?>
								<td>
									<?php if ($status['class'] == 'full'): ?>
										<!-- 満席の場合はセル表示 -->
										<div class="full">
											<span class="seat_symbol"><?= $status['symbol'] ?></span><span class="remain_seat"><?= $status['text'] ?></span>
										<?php else: ?>
											<!-- 空席の場合はボタン表示 -->
											<form method="POST" action="reserve_process.php" style="margin: 0;">
												<input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
												<input type="hidden" name="seat_type" value="counter">
												<button type="submit" class="seat-button <?= $status['class'] ?>">
													<span class="seat_symbol"><?= $status['symbol'] ?></span><span class="remain_seat"><?= $status['text'] ?></span>
												</button>
											</form>
										<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>

						<!-- テーブル席 -->
						<tr>
							<th class="seat-type"> <img src="../img/reserve/table.png" alt="テーブル席">テーブル</th>
							<?php foreach ($time_slots as $slot): ?>
								<?php $status = getSeatStatus($slot['table_remaining'], $num_people); ?>
								<td>
									<?php if ($status['class'] == 'full'): ?>
										<div class="full">
											<span class="seat_symbol"><?= $status['symbol'] ?></span><span class="remain_seat"><?= $status['text'] ?></span>
										</div>
									<?php else: ?>
										<form method="POST" action="reserve_process.php" style="margin: 0;">
											<input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
											<input type="hidden" name="seat_type" value="table">
											<button type="submit" class="seat-button <?= $status['class'] ?>">
												<span class="seat_symbol"><?= $status['symbol'] ?></span><span class="remain_seat"><?= $status['text'] ?></span>
											</button>
										</form>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>

						<!-- 座敷 -->
						<tr>
							<th class="seat-type"><img src="../img/reserve/zashiki.png" alt="座敷">座敷</th>
							<?php foreach ($time_slots as $slot): ?>
								<?php $status = getSeatStatus($slot['zashiki_remaining'], $num_people); ?>
								<td>
									<?php if ($status['class'] == 'full'): ?>
										<div class="full">
											<span class="seat_symbol"><?= $status['symbol'] ?></span><span class="remain_seat"><?= $status['text'] ?></span>
										</div>
									<?php else: ?>
										<form method="POST" action="reserve_process.php" style="margin: 0;">
											<input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
											<input type="hidden" name="seat_type" value="zashiki">
											<button type="submit" class="seat-button <?= $status['class'] ?>">
												<span class="seat_symbol"><?= $status['symbol'] ?></span><span class="remain_seat"><?= $status['text'] ?></span>
											</button>
										</form>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>
					</table>
				</div>


			</div>
	</main>
	<?php
	require_once __DIR__ . "/reserve_footer.php";
	?>
	<script src="../js/reserve_common.js"></script>
	<script src="../js/reserve.js"></script>
</body>

</html>