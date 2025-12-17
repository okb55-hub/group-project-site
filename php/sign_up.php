<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";
$error = '';
$step = 1; // 1:入力画面, 2:確認画面, 3:登録完了 この数字によってHTMLの表示を分岐

// 正規表現で前後の半角・全角スペースを調べて、消すための関数
function sanitize($str)
{
	return preg_replace('/^[\s　]+|[\s　]+$/u', '', $str);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// 確認ボタンを押した場合
	if (isset($_POST['confirm'])) {

		$name  = sanitize($_POST['name'] ?? '');
		$email = sanitize($_POST['email'] ?? '');
		$tel   = sanitize($_POST['tel'] ?? '');
		$password = $_POST['password'] ?? '';
		$password_confirm = $_POST['password_confirm'] ?? '';

		if ($name === '' || $tel === '' || $email === '' || $password === '') {
			$error = '必須項目が未入力です。';
			$step = 1;
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = '正しいメールアドレスを入力してください。';
			$step = 1;
		} elseif (!preg_match('/^\d{10,11}$/', $tel)) {
			$error = '電話番号は数字のみで10〜11桁で入力してください。';
			$step = 1;
		} elseif ($password !== $password_confirm) {
			$error = 'パスワードが一致しません';
			$step = 1;
		} else {
			// セッションに保存
			$_SESSION['name'] = $name;
			$_SESSION['tel'] = $tel;
			$_SESSION['email'] = $email;
			$_SESSION['password'] = $password;
			$step = 2; // 確認画面へ
		}
	}





	// 確認画面の戻るボタンを押した場合
	if (isset($_POST['back'])) {
		$step = 1;
	}

	// 確認画面の登録ボタンを押した場合
	if (isset($_POST['sign_up'])) {

		if (!isset($_SESSION['name'], $_SESSION['tel'], $_SESSION['email'], $_SESSION['password'])) {
			// セッションに必要なデータがない場合、入力画面に戻す
			$_SESSION['error_message'] = "セッション情報が無効です。最初からやり直してください。";
			header('Location: sign_up.php');
			exit;
		}
		$name = $_SESSION['name'];
		$tel = $_SESSION['tel'];
		$email = $_SESSION['email'];
		$password = $_SESSION['password'];

		// パスワードをハッシュ化
		$password_hashed = password_hash($password, PASSWORD_DEFAULT);

		// DB接続
		try {
			$db = getDb();
			$stt = $db->prepare("INSERT INTO users (name, tel, email, password) VALUES (?, ?, ?, ?)");
			$stt->bindValue(1, $name);
			$stt->bindValue(2, $tel);
			$stt->bindValue(3, $email);
			$stt->bindValue(4, $password_hashed);
			$stt->execute();

			// 登録したユーザーのIDを取得
			$user_id = $db->lastInsertId();

			$_SESSION['user_id'] = $user_id;

			// 登録時の一時データ（セッション）を削除
			unset($_SESSION['name']);
			unset($_SESSION['tel']);
			unset($_SESSION['email']);
			unset($_SESSION['password']);

			// 予約情報があれば予約確認画面へ
			if (isset($_SESSION['reservation'])) {
				header('Location: reserve_confirm.php');
				exit;
			} else {
				$step = 3; // 登録完了画面
			}
		} catch (PDOException $e) {
			if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
				$error = "このメールアドレスは既に登録されています。";
			} else {
				$error = "エラーが発生しました。時間をおいて再度お試しください。";
			}
			$step = 1; // 
		}
	}
}


?>


<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>新規会員登録</title>
	<link rel="stylesheet" href="./../css/reserve_common.css">
	<link rel="stylesheet" href="./../css/sign_up.css">

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

		<?php if ($step === 1): ?>
			<!-- 入力画面 -->
			<div class="sign_up_container">

				<h1>新規会員登録</h1>
				<div class="step-container">
					<div class="step-item active">入力</div>
					<div class="step-item">確認</div>
				</div>
				<!-- エラーがあれば赤字で表示 -->
				<?php if ($error): ?>
					<p class="error_message"><?= e($error) ?></p>
				<?php endif; ?>
				<p class="lead">
					会員登録により、来店のご予約を<br>
					web上からスムーズにご利用いただけます。
				</p>

				<form action="sign_up.php" method="post" class="sign_up_form">

					<div class="form_group">
						<label for="name">お名前</label>
						<input type="text" id="name" name="name"
							value="<?= isset($_SESSION['name']) ? e($_SESSION['name']) : '' ?>"
							placeholder="例）山田 太郎" required>
					</div>

					<div class="form_group">
						<label for="tel">電話番号</label>
						<input type="tel" id="tel" name="tel"
							value="<?= isset($_SESSION['tel']) ? e($_SESSION['tel']) : '' ?>"
							placeholder="例）09012345678（数字のみ）"
							title="数字のみで10〜11桁で入力してください"
							pattern="\d{10,11}" required>

					</div>

					<div class="form_group">
						<label for="email">メールアドレス</label>
						<input type="email" id="email" name="email"
							value="<?= isset($_SESSION['email']) ? e($_SESSION['email']) : '' ?>"
							placeholder="例）user@example.jp" required>

					</div>

					<div class="form_group">
						<label for="password">パスワード</label>
						<input type="password" id="password" name="password" required>
					</div>
					<div class="form_group">
						<label for="password_confirm">パスワード確認</label>
						<input type="password" id="password_confirm" name="password_confirm" required>
					</div>

					<div class="attention">
						<p>お預かりした個人情報の取り扱いについては、<br>
							当店の<span class="policy">「<a href="../html/policy.html" target="_blank" class="privacy_link">プライバシーポリシー
									<svg class="external_icon" viewBox="0 0 24 24">
										<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
										<polyline points="15 3 21 3 21 9" />
										<line x1="10" y1="14" x2="21" y2="3" />
									</svg>
								</a>」</span>をご確認ください。
						</p>
					</div>
					<div class="button">
						<button class="submit-button" type="submit" name="confirm">確認画面へ</button>
					</div>

				</form>

				<p class="to_login">
					すでにアカウントをお持ちの方は<br>
					<a href="login.php">ログインはこちら</a>
				</p>

			</div>
		<?php elseif ($step === 2): ?>
			<!-- 確認画面 -->
			<div class="sign_up_container">
				<h1>登録内容の確認</h1>

				<div class="step-container">
					<div class="step-item">入力</div>
					<div class="step-item active">確認</div>
				</div>

				<p>以下の内容で登録します。よろしいですか？</p>

				<div class="confirm_block">
					<h2>お客様情報</h2>
					<div class="confirm_detail">
						<p>お名前：<?= isset($_SESSION['name']) ? e($_SESSION['name']) : '' ?></p>
						<p>電話番号：<?= isset($_SESSION['tel']) ? e($_SESSION['tel']) : '' ?></p>
						<p>メール：<?= isset($_SESSION['email']) ? e($_SESSION['email']) : '' ?></p>
					</div>
				</div>

				<form action="sign_up.php" method="post">
					<div class="button">
						<button class="submit-button" type="submit" name="sign_up">登録を完了する</button>
					</div>
				</form>

				<form action="sign_up.php" method="post">
					<button class="back-button" type="submit" name="back">入力画面に戻る</button>
				</form>
			</div>
		<?php elseif ($step === 3): ?>
			<!-- 登録完了画面 -->
			<div class="sign_up_container">

				<div class="success-header">
					<div class="success-icon">✓</div>
					<h1>会員登録が完了しました！</h1>
					<p class="success-message">マイページより登録情報の確認が可能です。</p>
				</div>

				<div class="action-buttons">
					<a href="mypage.php" class="btn-primary">マイページへ</a>
					<a href="reserve.php" class="btn-secondary">トップページへ</a>
				</div>
			</div>
		<?php endif; ?>


	</main>
	<?php
	require_once __DIR__ . "/reserve_footer.php";
	?>
	<script src="../js/sign_up.js"></script>
</body>

</html>