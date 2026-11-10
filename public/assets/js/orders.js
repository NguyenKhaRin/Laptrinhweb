/**
 * ========================================================
 * ORDERS JS - XỬ LÝ NÚT ĐỔI TRẠNG THÁI GIAO DỊCH & ĐÁNH GIÁ
 * Phụ trách bởi: Thành viên phụ trách Module Đơn & Đánh giá
 * ========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // Xử lý nút thay đổi trạng thái đơn hàng (Xác nhận, Giao hàng, Hoàn tất, Hủy)
    const statusButtons = document.querySelectorAll('.btn-change-status');

    statusButtons.forEach(btn => {
        btn.addEventListener('click', async function () {
            const orderId = this.dataset.orderId;
            const newStatus = this.dataset.status;

            let confirmMsg = 'Bạn có chắc chắn muốn chuyển trạng thái đơn hàng này?';
            if (newStatus === 'cancelled') {
                confirmMsg = 'Bạn có chắc chắn muốn HỦY đơn hàng này? Hành động này không thể hoàn tác!';
            } else if (newStatus === 'completed') {
                confirmMsg = 'Bạn có chắc chắn đã nhận đủ tiền và hoàn tất giao dịch cho người mua?';
            }

            if (!confirm(confirmMsg)) {
                return;
            }

            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status', newStatus);

            try {
                this.disabled = true;
                const response = await fetch(window.location.origin + window.location.pathname.replace(/\/orders\/.*/, '/orders/update-status'), {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                // Tải lại trang để cập nhật giao diện mới nhất
                window.location.reload();
            } catch (error) {
                console.error('Lỗi cập nhật trạng thái đơn:', error);
                window.location.reload();
            }
        });
    });
});

