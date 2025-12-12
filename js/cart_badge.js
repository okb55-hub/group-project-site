'use strict';

document.querySelectorAll('.cart-button').forEach(btn => {
	btn.addEventListener('click', () => {
		const productId = btn.dataset.id;

		fetch('takeout.php', {
			method: 'POST',
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			body: `product_id=${productId}`
		})
		.then(res => res.json())
		.then(data => {

			if(data.status === 'ok') {
				document.getElementById('cart_count').textContent = data.cart_count;
				const modal = btn.closest('.modal');
				if (modal) {
					modal.style.transition = 'opacity 0.3s';
					modal.style.opacity = '0';

					setTimeout(() => {
						modal.style.display = 'none';
						modal.style.opacity = '1';
					}, 300);
				}
			} else {
				alert('カート追加に失敗しました');
			}
		})
		.catch(err => {
			console.error(err);
			alert('通信エラー');
		});
	});
});
