<?php
require_once __DIR__ . "/init.php";
require_once __DIR__ . "/DbManager.php";

// ログインしていなければログインページへ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $db = getDb();

    // ▼ ユーザー情報を取得
    $stt = $db->prepare("SELECT name, email, tel, created_at FROM users WHERE user_id = ?");
    $stt->execute([$user_id]);
    $user = $stt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
      echo "エラーが発生しました。"; 
	  exit;
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
</head>

<body>
    <div class="mypage_container">
        <h1>マイページ</h1>

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


        <div class="logout_area">
            <a href="logout.php" class="btn logout_btn">ログアウト</a>
        </div>
        
        <div class="back_link">
             <a href="reserve.php">← トップページへ戻る</a>
        </div>
    </div>
<script src="../js/reserve_common.js"></script>
</body>
</html>