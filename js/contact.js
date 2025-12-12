'use strict';

const form = document.querySelector("#contact_form form");
const modal = document.getElementById("confirmModal");
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

// 送信ボタンで完了画面表示（PHPはまだなし）
sendButton.addEventListener("click", function (e) {
    e.preventDefault(); // 本送信はまだ行わない
    modal.style.display = "none"; // モーダル閉じる
    thanksPage.style.display = "block"; // 送信完了画面表示
});

// トップへ戻るボタン
backToTop.addEventListener("click", function () {
    thanksPage.style.display = "none";
    window.scrollTo(0, 0);
});
