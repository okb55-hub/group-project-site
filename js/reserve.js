"use strict";
/* 日付選択の値を確認する処理 */
const dateInput = document.getElementById('reserve_date');
const reserveForm = document.querySelector('form');

// チェック処理の関数
function validateDate() {
	const val = dateInput.value;
	if (!val) return;

	const selectedDate = new Date(val);
	const dayOfWeek = selectedDate.getDay();
	// 今日の日付（時刻を00:00:00にリセット）
    const today = new Date();
    today.setHours(0, 0, 0, 0);

	if (dayOfWeek === 3) {
		dateInput.setCustomValidity('水曜日は定休日です。他の日付を選択してください。');
	} else if (selectedDate <= today) {
        dateInput.setCustomValidity('ご予約は明日以降の日付でお願いいたします。');
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

/* 席タイプのカードを押したときの処理 */
// 全ての席ボタン（div）を取得
const seat_card = document.querySelectorAll('.seat_card');
// 移動先の表を取得
const targetTable = document.getElementById('table_container');

// ループ処理で、各ボタンにクリック時の動きをつける
seat_card.forEach(button => {
    button.addEventListener('click', () => {
        // しゅっと移動
        targetTable.scrollIntoView({
            behavior: 'smooth',
            block: 'start' // 表の先頭が画面の上に来るように調整
        });
    });
});
