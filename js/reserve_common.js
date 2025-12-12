"use strict";

console.log("読み込まれてます");

// ヘッダーのハンバーガーメニューの開閉
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');

    if (hamburger && mobileMenu) {
        // クリックで開閉
        hamburger.addEventListener('click', function (e) {
            e.stopPropagation(); // クリックが親に伝播しないように
            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });

        // メニュー内クリックは閉じない
        mobileMenu.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // 画面幅が広くなったら強制的に閉じる
        window.addEventListener('resize', function () {
            if (window.innerWidth > 480) {
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });

        // 外側クリックで閉じる
        document.addEventListener('click', function () {
            hamburger.classList.remove('active');
            mobileMenu.classList.remove('active');
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