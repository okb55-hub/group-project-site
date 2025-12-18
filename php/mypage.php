<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

// ログインしていなければログインページへ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$error = '';
$user = null;
$display_name = "取得できませんでした";
$is_logged_in = false;
$is_Error = false;

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

    // ▼ ユーザー情報を取得
    $stt = $db->prepare("SELECT name, email, tel, created_at FROM users WHERE user_id = ?");
    $stt->execute([$user_id]);
    $user = $stt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "エラーが発生しました。<br>時間をおいて再度お試しください。";

    // 例外のクラスによってログの識別子を変える
    if ($e instanceof PDOException) {
        error_log("DB接続エラー [mypage.php]: " . $e->getMessage());
    } else {
        error_log("その他のエラー [mypage.php]: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link rel="stylesheet" href="../css/reserve_common.css">
    <link rel="stylesheet" href="../css/mypage.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@400;700&display=swap" rel="stylesheet">

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
        <div class="mypage_container">
            <h1>マイページ</h1>
            <?php if ($error): ?>
                <p class="error_message"><?= $error ?></p>
            <?php endif; ?>

            <?php if (!$error && $user): ?>
                <section class="info_block profile">
                    <h2>登録情報</h2>
                    <div class="info_content">
                        <div class="detail_row">
                            <span class="label">氏名</span>
                            <span class="value"><?= e($user['name']) ?></span>
                        </div>
                        <div class="detail_row">
                            <span class="label">メール</span>
                            <span class="value"><?= e($user['email']) ?></span>
                        </div>
                        <div class="detail_row">
                            <span class="label">電話番号</span>
                            <span class="value"><?= e($user['tel']) ?></span>
                        </div>
                        <div class="detail_row">
                            <span class="label">登録日</span>
                            <span class="value"><?= date('Y年n月j日', strtotime($user['created_at'])) ?></span>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <div class="logout_area">
                <a href="logout.php" class="btn logout_btn">ログアウト</a>
            </div>

            <div class="back_link">
                <a href="reserve.php">← トップページへ戻る</a>
            </div>
        </div>
    </main>
    <?php
    require_once __DIR__ . "/reserve_footer.php";
    ?>
    <script src="../js/reserve_common.js"></script>
</body>

</html>