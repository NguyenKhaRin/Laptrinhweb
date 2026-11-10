<?php
$currentUser = $_SESSION['user'] ?? null;
$customCss = ['products'];
?>
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/?category_id=<?= $product['category_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['title']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Ảnh sản phẩm -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid w-100 detail-product-image" alt="<?= htmlspecialchars($product['title']) ?>" onerror="this.src='https://placehold.co/800x600?text=No+Image'">
            </div>

            <!-- Mô tả chi tiết -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Mô tả sản phẩm</h5>
                <div class="product-description text-secondary" style="white-space: pre-line; line-height: 1.7;">
                    <?= htmlspecialchars($product['description']) ?>
                </div>
            </div>
        </div>

        <!-- Thông tin & Mua hàng / Liên hệ -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 80px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                        <?php 
                            echo match($product['condition']) {
                                'new' => 'Mới 100%',
                                'like_new' => 'Như mới 99%',
                                'good' => 'Tốt 90%',
                                default => 'Dùng ổn'
                            };
                        ?>
                    </span>
                </div>

                <h3 class="fw-bold mb-3"><?= htmlspecialchars($product['title']) ?></h3>

                <!-- Giá bán -->
                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="display-6 fw-bold text-danger"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                        <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                            <span class="text-muted text-decoration-line-through"><?= number_format($product['original_price'], 0, ',', '.') ?> đ</span>
                            <span class="badge bg-danger">Giảm <?= round((1 - $product['price'] / $product['original_price']) * 100) ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="small text-muted mt-2">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Khu vực: <strong><?= htmlspecialchars($product['location']) ?></strong>
                    </div>
                </div>

                <!-- Thẻ Người bán -->
                <div class="card border border-light-subtle rounded-3 p-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 50px; height: 50px; font-size: 20px;">
                            <?= mb_strtoupper(mb_substr($product['seller_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">
                                <a href="<?= BASE_URL ?>/profile/seller/<?= $product['user_id'] ?>" class="text-dark text-decoration-none hover-primary">
                                    <?= htmlspecialchars($product['seller_name']) ?>
                                </a>
                            </h6>
                            <div class="text-warning small">
                                <i class="bi bi-star-fill"></i> <?= $product['seller_rating'] ?? '5.0' ?>
                                <span class="text-muted">(<?= $product['seller_reviews_count'] ?? 0 ?> đánh giá)</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted">
                        <i class="bi bi-telephone-fill me-1"></i> SĐT: <?= !empty($product['seller_phone']) ? htmlspecialchars($product['seller_phone']) : 'Chưa cập nhật' ?>
                    </div>
                </div>

                <!-- Hành động mua hàng / Chat -->
                <div class="d-grid gap-2">
                    <?php if ($product['status'] === 'available'): ?>
                        <?php if ($currentUser && $currentUser['id'] == $product['user_id']): ?>
                            <div class="alert alert-info text-center mb-0">Đây là bài đăng bán của chính bạn.</div>
                        <?php else: ?>
                            <!-- Nút Mua ngay (Mở Modal Đặt hàng) -->
                            <button type="button" class="btn btn-danger btn-lg fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#orderModal">
                                <i class="bi bi-bag-check-fill me-1"></i> Đặt Mua Ngay
                            </button>

                            <!-- Nút Nhắn tin với người bán -->
                            <a href="<?= BASE_URL ?>/chat?partner_id=<?= $product['user_id'] ?>&product_id=<?= $product['id'] ?>" class="btn btn-outline-primary btn-lg fw-bold rounded-3">
                                <i class="bi bi-chat-dots-fill me-1"></i> Chat với người bán
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg fw-bold disabled">
                            <?= $product['status'] === 'sold' ? 'Đã Bán Hết' : 'Đã có người đặt' ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đặt Hàng Nhanh -->
<?php if ($product['status'] === 'available'): ?>
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Xác nhận Đặt mua sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>/orders/store" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="modal-body">
                    <div class="d-flex gap-3 mb-3 p-2 bg-light rounded-3">
                        <img src="<?= htmlspecialchars($product['image']) ?>" class="rounded-2" style="width: 60px; height: 60px; object-fit: cover;">
                        <div>
                            <div class="fw-bold small"><?= htmlspecialchars($product['title']) ?></div>
                            <div class="text-danger fw-bold"><?= number_format($product['price'], 0, ',', '.') ?> đ</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số điện thoại nhận hàng <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" required value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" placeholder="09xxxxxxxx">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ giao dịch / Nhận hàng <span class="text-danger">*</span></label>
                        <input type="text" name="shipping_address" class="form-control" required value="<?= htmlspecialchars($currentUser['address'] ?? '') ?>" placeholder="KTX Khu B, Phòng 304...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú cho người bán</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ví dụ: Giao sau giờ hành chính, gọi trước khi đến..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary fw-bold">Xác nhận đặt hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

