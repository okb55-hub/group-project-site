<header class="reserve_header">
    <div class="header_inner">
        <div class="header_left">
            <a href="reserve.php" class="header_logo">本格韓国料理 ソダム</a>
            <span class="system_label">Reservation</span>
        </div>

        <!-- PC版：ナビゲーション -->
        <nav class="header_nav">
            <!-- ここにindex.phpのURL入れる -->
            <a href="https://example.com" target="_blank" class="store_link">
                店舗サイトへ
                <svg class="external_icon" viewBox="0 0 24 24">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                    <polyline points="15 3 21 3 21 9" />
                    <line x1="10" y1="14" x2="21" y2="3" />
                </svg>
            </a>
            <div class="divider"></div>
            <?php if ($is_logged_in): ?>
                <a href="mypage.php" class="user_info nav_link">
                      <svg class="user_icon" viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="7.5" r="4.0" stroke-linecap="round" />
                    <path d="M20 22C18.2 18.5 15 16.5 12 16.5C9 16.5 5.8 18.5 4 22"stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                    <span class="user_name"><?= e($display_name) ?></span><span class="honorific">さん</span>
                </a>
            <?php else: ?>
                <div class="user_info guest"> <svg class="user_icon" viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="7.5" r="4.0" stroke-linecap="round" />
                    <path d="M20 22C18.2 18.5 15 16.5 12 16.5C9 16.5 5.8 18.5 4 22"stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                    <span class="user_name"><?= e($display_name) ?></span><span class="honorific">さん</span>
                </div>
            <?php endif; ?>
            <?php if ($is_logged_in): ?>
                <a href="reservation_history.php" class="nav_link">予約履歴</a>
                <a href="logout.php" class="action_btn logout_btn">ログアウト</a>
            <?php else: ?>
                <a href="login.php" class="nav_link">ログイン</a>
                <a href="sign_up.php" class="action_btn">新規登録</a>
            <?php endif; ?>
        </nav>

        <!-- スマホ版：ハンバーガーメニュー -->
        <button class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- モバイルメニュー -->
<div class="mobile_menu" id="mobileMenu">
    <div class="mobile_menu_inner">
        <?php if ($is_logged_in): ?>
            <div class="user_info">
                <a href="mypage.php" class="nav_link">
            <span class="user_name"><?= $display_name ?></span><span class="honorific">さん</span>
            </a>
        </div>
        <?php else: ?>
            <div class="user_info">
            <span class="user_name"><?= $display_name ?></span><span class="honorific">さん</span>
        </div>
        <?php endif; ?>
        
        <div class="divider"></div>
        <a href="https://example.com" target="_blank" class="store_link">
            店舗サイトへ
            <svg class="external_icon" viewBox="0 0 24 24">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                <polyline points="15 3 21 3 21 9" />
                <line x1="10" y1="14" x2="21" y2="3" />
            </svg>
        </a>
        <?php if ($is_logged_in): ?>
            <a href="reservation_history.php" class="nav_link">予約履歴</a>
            <a href="logout.php" class="action_btn logout_btn">ログアウト</a>
        <?php else: ?>
            <a href="login.php" class="nav_link">ログイン</a>
            <a href="sign_up.php" class="action_btn">新規登録</a>
        <?php endif; ?>
    </div>
</div>