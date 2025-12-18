'use strict';

const payButton = document.getElementById('payButton');

payButton.addEventListener('click', function () {
	setTimeout(() => {
		window.location.href = 'order_complete.php?paypay_success=1';
	}, 2500);
});