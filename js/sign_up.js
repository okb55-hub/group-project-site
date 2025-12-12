"use strict";
		const form = document.querySelector('.sign_up_form');
		if (form) {
			form.addEventListener('submit', function(e) {
				const password = document.querySelector('#password');
				const passwordConfirm = document.querySelector('#password_confirm');

				if (password && passwordConfirm) { // ← 存在チェック
					if (password.value !== passwordConfirm.value) {
						e.preventDefault();
						alert('パスワードと確認用パスワードが一致しません');
					}
				}
			});
		}