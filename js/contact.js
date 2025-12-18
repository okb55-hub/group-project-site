'use strict';

const form = document.querySelector("#contact_form form");
const modal = document.getElementById("confirm_modal");
const closeBtn = document.querySelector('.modal_close');
const backButton = document.getElementById("backButton");
const sendButton = document.getElementById("sendButton");
const thanksPage = document.getElementById("thanksPage");
const backToTop = document.getElementById("backToTop");

// フォーム送信 → モーダル表示
form.addEventListener("submit", function (e) {
    e.preventDefault(); // 本送信を一旦止める

    // 入力値を反映
    document.getElementById("confirm-name").textContent = form.name.value;
    document.getElementById("confirm-kana").textContent = form.kana.value;
    document.getElementById("confirm-email").textContent = form.email.value;
    document.getElementById("confirm-tel").textContent = form.tel.value;
    document.getElementById("confirm-genre").textContent = form['select-box'].selectedOptions[0].text;
    document.getElementById("confirm-message").textContent = form.message.value;

    // モーダル表示
    modal.style.display = "block";
});

// モーダルを閉じる
closeBtn.addEventListener('click', function() {
	modal.style.display = "none";
})
// 修正ボタンでモーダルを閉じる
backButton.addEventListener("click", function () {
    modal.style.display = "none";
});

// モーダル背景クリックで閉じる
modal.addEventListener("click", function (e) {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});



// トップへ戻るボタン
backToTop.addEventListener("click", function () {
    // ページを今のURLで読み込み直す（これで勝手に一番上に戻り、表示も元通りになります）
    location.reload();
});

// 送信完了画面を表示する処理
sendButton.addEventListener("click", function (e) {
    e.preventDefault();
    modal.style.display = "none";

    const titleSection = document.querySelector(".title_section");
    const mainSection = document.querySelector(".main_section");

    if (titleSection) titleSection.style.display = "none";
    if (mainSection) mainSection.style.display = "none";

    thanksPage.style.display = "block";
    window.scrollTo(0, 0);
});
