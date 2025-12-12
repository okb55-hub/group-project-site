'use strict';

const confirmBtn = document.getElementById('confirm_btn');
const modal = document.getElementById('confirm_modal');
const closeBtn = document.querySelector('.modal_close');
const editBtn = document.querySelector('.modal_edit_btn');
const errorMessages = document.getElementById('error_messages');

// 確認ボタンクリック
confirmBtn.addEventListener('click', function() {
	// バリデーション
	const errors = validateForm();
	
	if (errors.length > 0) {
		showErrors(errors);
		return;
	}

	// エラークリア
	errorMessages.style.display = 'none';

	// フォームデータ取得
	const formData = getFormData();

	// モーダルに値をセット
	setModalData(formData);

	// モーダル表示
	modal.style.display = 'flex';
	document.body.style.overflow = 'hidden';
});

// モーダルを閉じる
closeBtn.addEventListener('click', function() {
	closeModal();
})

// 修正ボタンでモーダル閉じる
editBtn.addEventListener('click', function() {
	closeModal();
})

// モーダル外クリックでモーダル閉じる
window.addEventListener('click', function(event) {
	if (event.target === modal) {
		closeModal();
	}
});

// モーダル閉じる関数
function closeModal() {
	modal.style.display = 'none';
	document.body.style.overflow = '';
}

// 日付をフォーマット
function formatDate(dateString) {
	if (!dateString) return '';

	const date = new Date(dateString);
	const year = date.getFullYear();
	const month = date.getMonth() + 1;
	const day = date.getDate();

	// 曜日
	const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
	const weekday = weekdays[date.getDay()];

	return `${year}年${month}月${day}日（${weekday}）`;
}

// フォームデータ取得
function getFormData() {
	return {
		name: document.getElementById('name').value.trim(),
		tel: document.getElementById('tel').value.trim(),
		email: document.getElementById('email').value.trim(),
		pickup_date: document.getElementById('pickup_date').value,
		pickup_time: document.getElementById('pickup_time').value,
		payment_method: document.querySelector('input[name="payment_method"]:checked')?.value,
	};
}

// バリデーション
function validateForm() {
	const errors = [];
	const formData = getFormData();

	if (!formData.name) {
		errors.push('お名前を入力してください');
	}

	if (!formData.tel) {
		errors.push('電話番号を入力してください');
	} else if (!/^[0-9-]+$/.test(formData.tel)) {
		errors.push('電話番号は数字とハイフン(-)のみで入力してください');
	}

	if (!formData.email) {
		errors.push('メールアドレスを入力してください');
	} else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
		errors.push('正しいメールアドレスを入力してください');
	}

	if (!formData.pickup_date) {
		errors.push('受け取り希望日を選択してください');
	}

	if (!formData.pickup_time) {
		errors.push('受け取り希望時間を選択してください');
	}

	if (!formData.payment_method) {
		errors.push('決済方法を選択してください');
	}

	return errors;
}

// エラー表示
function showErrors(errors) {
	errorMessages.innerHTML = errors.map(err =>
		`<p class="error">※ ${err}</p>`
	).join('');

	errorMessages.style.display = 'block';

	// エラーメッセージまでスクロール
	errorMessages.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// モーダルにデータをセット
function setModalData(formData) {
	// お客様情報
	document.getElementById('confirm_name').textContent = formData.name;
	document.getElementById('confirm_tel').textContent = formData.tel;
	document.getElementById('confirm_email').textContent = formData.email;
	
	// 受け取り日時
	const formattedDate = formatDate(formData.pickup_date);
	const datetime = `${formattedDate} ${formData.pickup_time}`;
	document.getElementById('confirm_datetime').textContent = datetime;

	// 決済方法
	const paymentText = formData.payment_method === 'store'
		? '店頭支払い（現金）'
		: '事前決済（クレジットカード・PayPay）';
	document.getElementById('confirm_payment').textContent = paymentText;

	// 送信ボタンのテキスト変更
	const submitText = document.getElementById('submit_text');
	if (formData.payment_method === 'store') {
		submitText.textContent = '注文を確定する';
	} else {
		submitText.textContent = '決済に進む';
	}

	// hiddenフィールドにデータをセット
	document.getElementById('hidden_name').value = formData.name;
	document.getElementById('hidden_tel').value = formData.tel;
	document.getElementById('hidden_email').value = formData.email;
	document.getElementById('hidden_pickup_date').value = formData.pickup_date;
	document.getElementById('hidden_pickup_time').value = formData.pickup_time;
	document.getElementById('hidden_payment_method').value = formData.payment_method;
}
