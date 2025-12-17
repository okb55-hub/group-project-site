"use strict";
document.getElementById('reserve_date').addEventListener('change', function (e) {
	const selectedDate = new Date(e.target.value);
	const dayOfWeek = selectedDate.getDay();

	if (dayOfWeek === 3) {
		// 水曜日の場合、バリデーションエラーを設定
		e.target.setCustomValidity('水曜日は定休日です。他の日付を選択してください。');
	} else {
		// それ以外はエラーをクリア
		e.target.setCustomValidity('');
	}
});

// フォーム送信時にも再チェック
document.querySelector('form').addEventListener('submit', function (e) {
	const dateInput = document.getElementById('reserve_date');
	const selectedDate = new Date(dateInput.value);
	const dayOfWeek = selectedDate.getDay();

	if (dayOfWeek === 3) {
		dateInput.setCustomValidity('水曜日は定休日です。他の日付を選択してください。');
		e.preventDefault();
	} else {
		dateInput.setCustomValidity('');
	}
});