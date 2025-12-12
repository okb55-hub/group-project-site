'use strict';
// モーダル表示
document.querySelectorAll('.open-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        const target = this.dataset.target;
        document.getElementById(target).style.display = 'flex';
    });
});

document.querySelectorAll('.modal .close').forEach(closeBtn => {
    closeBtn.addEventListener('click', function() {
        this.closest('.modal').style.display = 'none';
    });
});

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    // ページ読み込み時に "selected" を付ける
    if (typeof cartItems !== "undefined") {
        cartItems.forEach(id => {
            const item = document.querySelector(`.takeout_item[data-id="${id}"]`);
            if (item) {
                item.classList.add("selected");
            }
        });
    }
});


// 選択済みのカートの色を変える
document.querySelectorAll('.cart-button').forEach(btn => {
    btn.addEventListener('click', () => {
        const productId = btn.dataset.id;

        fetch('takeout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {

                // ★ 一覧の商品枠に selected を付ける
                const item = document.querySelector(`.takeout_item[data-id="${productId}"]`);
                if (item) {
                    item.classList.add("selected");
                }

                // ★ カート数のバッジ更新
                document.getElementById('cart_count').textContent = data.cart_count;
            }
        });
    });
});
