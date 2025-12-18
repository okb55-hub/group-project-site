'use strict';

// 数量変更
document.querySelectorAll(".item_quantity").forEach(select => {
    select.addEventListener("change", function () {
        let id = this.dataset.id;
        let qty = this.value;

        fetch("cart_update.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `product_id=${id}&quantity=${qty}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "ok") {
                // 小計を更新
                const cartContainer = this.closest(".cart_item");
                const subtotalElement = cartContainer.querySelector(".item_sum");
                subtotalElement.textContent = `￥${data.subtotal}`;

                // 合計金額を更新
                document.getElementById("total_price").textContent = `￥${data.total}`;

                // バッジ更新
                document.getElementById("cart_count").textContent = data.cart_count;
                // 合計点数の更新
                document.getElementById("total_items").textContent = data.total_items;
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('更新に失敗しました');
        });
    });
});


// 削除
document.querySelectorAll(".delete_btn").forEach(btn => {
    btn.addEventListener("click", function () {
        let id = this.dataset.id;

        // 確認ダイアログ（任意）
        if (!confirm('この商品を削除しますか?')) {
            return;
        }

        fetch("cart_update.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `remove_id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "ok") {
                // 該当商品をフェードアウトして削除
                const cartContainer = btn.closest(".cart_item");
                cartContainer.style.transition = "opacity 0.3s";
                cartContainer.style.opacity = "0";
                
                setTimeout(() => {
                    cartContainer.remove();

                    // バッジ更新
                    document.getElementById("cart_count").textContent = data.cart_count;

                    // 合計金額を更新
                    document.getElementById("total_price").textContent = `￥${data.total}`;

                    // 合計点数を表示
                    document.getElementById("total_items").textContent = data.total_items;

                    // 0になったら文言表示
                    if (data.cart_count == 0) {
                        document.querySelector(".cart_container_wrapper").innerHTML =
                            "<h1>カート内の商品</h1><p>カートに商品は入っていません。</p>";
                    }
                }, 300);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('削除に失敗しました');
        });
    });
});
