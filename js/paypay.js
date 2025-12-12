'use strict';

document.getElementById('payButton').addEventListener('click', function () {
	this.disabled = true;
	this.innerText = "支払い処理中...";

	setTimeout(() => {
		window.location.href = 'order_complete.php?paypay_success=1';
	}, 2500);
});