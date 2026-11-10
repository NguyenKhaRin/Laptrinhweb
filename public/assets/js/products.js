/**
 * ========================================================
 * PRODUCTS JS - XỬ LÝ SỰ KIỆN SẢN PHẨM & TÌM KIẾM
 * Phụ trách bởi: Thành viên phụ trách Module Sản phẩm
 * ========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Preview ảnh trước khi đăng bán sản phẩm
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');

    if (imageInput && imagePreview && previewContainer) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                // Kiểm tra kích thước file (tối đa 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ảnh không được vượt quá 5MB. Vui lòng chọn ảnh khác!');
                    this.value = '';
                    previewContainer.classList.add('d-none');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('d-none');
            }
        });
    }

    // 2. Tự động submit bộ lọc khi thay đổi dropdown sắp xếp
    const sortSelect = document.querySelector('select[name="sort"]');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const form = document.getElementById('filterForm');
            if (form) form.submit();
        });
    }
});

