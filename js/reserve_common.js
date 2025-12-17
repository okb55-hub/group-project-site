"use strict";

// ヘッダーのハンバーガーメニューの開閉
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobile_menu_overlay');

    if (hamburger && mobileMenu && mobileMenuOverlay) {
        // ハンバーガーボタンクリックで開閉
        hamburger.addEventListener('click', function (e) {
            e.stopPropagation();
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
        });

        // オーバーレイクリックで閉じる
        mobileMenuOverlay.addEventListener('click', function () {
            hamburger.classList.remove('active');
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
        });

        // 画面幅が広くなったら強制的に閉じる
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active'); // オーバーレイも閉じる
            }
        });
    }
});

// ログアウトボタンを押したときの確認ダイアログ 
document.addEventListener('DOMContentLoaded', () => {
    const logoutLinks = document.querySelectorAll('.logout_btn'); 

    if (logoutLinks.length > 0) {
        logoutLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const confirmed = confirm('ログアウトしますか？');
                if (!confirmed) {
                    e.preventDefault(); 
                }
            });
        });
    }
});