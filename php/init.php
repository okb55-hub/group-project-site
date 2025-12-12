<?php
// 各ページの初期設定
// セッションがまだ開始されてない場合に開始
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/Encode.php";
date_default_timezone_set('Asia/Tokyo');