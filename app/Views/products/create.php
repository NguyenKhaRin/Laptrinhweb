<?php
$customCss = ['products'];
$customJs  = ['products'];
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-warning text-dark p-3 rounded-circle me-3">
                        <i class="bi bi-tag-fill fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1">Đăng Bán Đồ Cũ</h3>
                        <p class="text-muted mb-0">Cung cấp thông tin chi tiết và hình ảnh rõ ràng để bán nhanh hơn</p>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/products/store" method="POST" enctype="multipart/form-data" id="createProductForm">
                    <!-- Tiêu đề sản phẩm -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Tiêu đề tin đăng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="VD: Laptop Dell Latitude 7490 i5 8GB SSD 256GB" required>
                    </div>

                    <div class="row">
                        <!-- Danh mục -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tình trạng -->
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label fw-bold">Tình trạng đồ dùng <span class="text-danger">*</span></label>
                            <select class="form-select" id="condition" name="condition" required>
                                <option value="like_new">Như mới 99% (Dùng rất ít)</option>
                                <option value="good" selected>Còn tốt 90% (Hoạt động hoàn hảo)</option>
                                <option value="fair">Dùng ổn (Có trầy xước nhẹ)</option>
                                <option value="new">Mới 100% (Chưa qua sử dụng)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Giá bán -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-bold">Giá bán thanh lý (VNĐ) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" placeholder="VD: 500000" min="0" step="1000" required>
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <!-- Giá gốc lúc mua -->
                        <div class="col-md-6 mb-3">
                            <label for="original_price" class="form-label fw-bold">Giá mua mới ban đầu (tùy chọn)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="original_price" name="original_price" placeholder="VD: 1200000" min="0" step="1000">
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Khu vực người bán -->
                    <div class="mb-3">
                        <label for="location" class="form-label fw-bold">Khu vực / Địa điểm giao dịch <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="VD: KTX Khu B ĐHQG / Cầu Giấy, Hà Nội..." required>
                    </div>

                    <!-- Hình ảnh sản phẩm -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-bold">Ảnh sản phẩm <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="imageInput" name="image" accept="image/*" required>
                        <div class="form-text">Định dạng JPG, PNG, WEBP. Dung lượng tối đa 5MB.</div>
                        
                        <!-- Khung xem trước ảnh preview -->
                        <div class="mt-3 d-none" id="previewContainer">
                            <img id="imagePreview" src="#" alt="Preview ảnh" class="img-thumbnail rounded-3" style="max-height: 250px;">
                        </div>
                    </div>

                    <!-- Mô tả chi tiết -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Mô tả chi tiết sản phẩm <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Mô tả nguồn gốc, thời gian sử dụng, phụ kiện kèm theo, lý do bán..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>/" class="btn btn-light px-4">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Đăng Bán Ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

