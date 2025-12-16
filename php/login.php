<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

$error = '';

if (isset($_SESSION['user_id'])) {
    header('Location: mypage.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // 1.入力欄の空チェック
    if ($email === '' || $password === '') {
        $error = 'メールアドレスとパスワードを入力してください。';
    } else {
        try {
            $db = getDb();
            $stt = $db->prepare("SELECT user_id, password FROM users WHERE email = ?");
            $stt->bindValue(1, $email);
            $stt->execute();
            $user = $stt->fetch(PDO::FETCH_ASSOC);

            // 2．メールアドレス不一致
            if (!$user) {
                $error = 'メールアドレスまたはパスワードが違います。';
            }
            // 3.パスワード不一致
            elseif (!password_verify($password, $user['password'])) {
                $error = 'メールアドレスまたはパスワードが違います。';
            }
            // 4.ログイン成功
            else {
                $_SESSION['user_id'] = $user['user_id'];

                // 予約途中だった場合は確認画面へ
                if (isset($_SESSION['reservation'])) {
                    header('Location: reserve_confirm.php');
                    exit;
                } else {
                    header('Location: mypage.php');
                    exit;
                }
            }
        } catch (Exception $e) {
            $error = "エラーが発生しました。<br>時間をおいて再度お試しください。";
            
            // 例外のクラスによってログの識別子を変える
            if ($e instanceof PDOException) {
                error_log("DB接続エラー [login.php]: " . $e->getMessage());
            } else {
                error_log("その他のエラー [login.php]: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="../css/reserve_common.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="login_container">
        <form action="login.php" method="post" class="login_form">

            <!-- エラーがある場合ここに表示 -->
            <?php if ($error): ?>
                <p class="error_message"><?= $error ?></p>
            <?php endif; ?>


            <div class="form_group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form_group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="button">
                <button class="submit-button" type="submit" name="confirm">ログイン</button>
            </div>
        </form>
        <p class="to_signup">アカウントをお持ちでない方は<br>
            <a href="sign_up.php">新規登録はこちら</a>
        </p>
    </div>
</body>

</html>