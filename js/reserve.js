"use strict";

const dateInput = document.getElementById('reserve_date');
const reserveForm = document.querySelector('form');

// チェック処理の関数
function validateDate() {
	const val = dateInput.value;
	if (!val) return;
	const selectedDate = new Date(val);
	const dayOfWeek = selectedDate.getDay();
	if (dayOfWeek === 3) {
		dateInput.setCustomValidity('水曜日は定休日です。他の日付を選択してください。');
	} else {
		dateInput.setCustomValidity('');
	}
}

// 1. 値が変わった時
dateInput.addEventListener('change', function () {
	validateDate();
	dateInput.reportValidity();
});

// 2. ページが読み込まれた時
window.addEventListener('DOMContentLoaded', function () {
	validateDate();
});

// 3. 送信時

reserveForm.addEventListener('submit', function (e) {
	validateDate(); // 最新の状態をチェック
	if (!dateInput.checkValidity()) {
		// エラーがあれば送信を止めて吹き出しを出す
		dateInput.reportValidity();
		e.preventDefault();
	}
});
