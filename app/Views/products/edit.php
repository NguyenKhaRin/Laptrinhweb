<?php
$customCss = ['products'];
$customJs  = ['products'];
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-primary text-white p-3 rounded-circle me-3">
                        <i class="bi bi-pencil-square fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1">Chỉnh Sửa Tin Đăng</h3>
                        <p class="text-muted mb-0">Cập nhật thông tin chi tiết hoặc trạng thái của sản phẩm</p>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
                    <!-- Tiêu đề sản phẩm -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Tiêu đề tin đăng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
                    </div>

                    <div class="row">
                        <!-- Danh mục -->
                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tình trạng -->
                        <div class="col-md-4 mb-3">
                            <label for="condition" class="form-label fw-bold">Tình trạng <span class="text-danger">*</span></label>
                            <select class="form-select" id="condition" name="condition" required>
                                <option value="new" <?= ($product['condition'] === 'new') ? 'selected' : '' ?>>Mới 100%</option>
                                <option value="like_new" <?= ($product['condition'] === 'like_new') ? 'selected' : '' ?>>Như mới 99%</option>
                                <option value="good" <?= ($product['condition'] === 'good') ? 'selected' : '' ?>>Còn tốt (90%)</option>
                                <option value="fair" <?= ($product['condition'] === 'fair') ? 'selected' : '' ?>>Dùng ổn</option>
                            </select>
                        </div>

                        <!-- Trạng thái bán -->
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-bold">Trạng thái bán</label>
                            <select class="form-select" id="status" name="status">
                                <option value="available" <?= ($product['status'] === 'available') ? 'selected' : '' ?>>Đang rao bán</option>
                                <option value="reserved" <?= ($product['status'] === 'reserved') ? 'selected' : '' ?>>Có người đặt hẹn</option>
                                <option value="sold" <?= ($product['status'] === 'sold') ? 'selected' : '' ?>>Đã bán hết</option>
                                <option value="hidden" <?= ($product['status'] === 'hidden') ? 'selected' : '' ?>>Tạm ẩn tin</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Giá bán -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-bold">Giá thanh lý (VNĐ) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" value="<?= (int)$product['price'] ?>" min="0" step="1000" required>
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <!-- Giá gốc -->
                        <div class="col-md-6 mb-3">
                            <label for="original_price" class="form-label fw-bold">Giá mua mới ban đầu</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="original_price" name="original_price" value="<?= $product['original_price'] ? (int)$product['original_price'] : '' ?>" min="0" step="1000">
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Khu vực -->
                    <div class="mb-3">
                        <label for="location" class="form-label fw-bold">Khu vực giao dịch <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($product['location']) ?>" required>
                    </div>

                    <!-- Ảnh hiện tại & Upload ảnh mới -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ảnh sản phẩm hiện tại</label>
                        <div class="mb-2">
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="Ảnh hiện tại" class="rounded-3 border" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <label for="imageInput" class="form-label fw-bold">Thay đổi ảnh khác (tùy chọn)</label>
                        <input class="form-control" type="file" id="imageInput" name="image" accept="image/*">
                        <div class="mt-2 d-none" id="previewContainer">
                            <img id="imagePreview" src="#" alt="Preview" class="img-thumbnail rounded-3" style="max-height: 150px;">
                        </div>
                    </div>

                    <!-- Mô tả chi tiết -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">Mô tả chi tiết <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/products/detail/<?= $product['id'] ?>" class="btn btn-light px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="bi bi-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

