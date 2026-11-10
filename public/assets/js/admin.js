/**
 * ========================================================
 * ADMIN JS - XỬ LÝ SỰ KIỆN QUẢN TRỊ VIÊN & BẢNG ĐIỀU KHIỂN
 * Phụ trách bởi: TV5 (Frontend Engineer 2)
 * ========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // Xác nhận khi xóa bài đăng hoặc thay đổi vai trò
    const deleteButtons = document.querySelectorAll('.btn-admin-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Hành động quản trị: Bạn có chắc chắn muốn xóa mục này?')) {
                e.preventDefault();
            }
        });
    });
});
