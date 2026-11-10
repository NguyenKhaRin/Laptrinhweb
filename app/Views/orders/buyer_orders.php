<?php
$customCss = ['orders'];
$customJs  = ['orders'];
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-cart3 text-primary me-2"></i>Đơn Hàng Tôi Đã Mua</h3>
            <p class="text-muted mb-0">Theo dõi trạng thái các sản phẩm đồ cũ bạn đã đặt</p>
        </div>
        <a href="<?= BASE_URL ?>/" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-plus-lg me-1"></i> Mua thêm đồ khác
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <i class="bi bi-bag-x text-muted display-3 mb-3"></i>
            <h5>Bạn chưa đặt mua món đồ nào!</h5>
            <p class="text-muted">Khám phá ngay hàng trăm món đồ cũ thanh lý giá hời.</p>
            <div>
                <a href="<?= BASE_URL ?>/" class="btn btn-primary rounded-pill px-4">Dạo chợ ngay</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($orders as $order): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-3 order-card">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-3 gap-2">
                            <div>
                                <span class="fw-bold text-dark me-2">Mã đơn: #<?= htmlspecialchars($order['order_code']) ?></span>
                                <span class="text-muted small"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                            </div>
                            <div>
                                <?php
                                    $statusBadge = match($order['status']) {
                                        'pending'   => ['bg-warning text-dark', 'Chờ người bán xác nhận'],
                                        'confirmed' => ['bg-info text-dark', 'Đã xác nhận đơn'],
                                        'shipping'  => ['bg-primary text-white', 'Đang giao dịch / Giao hàng'],
                                        'completed' => ['bg-success text-white', 'Giao dịch thành công'],
                                        'cancelled' => ['bg-danger text-white', 'Đã hủy đơn'],
                                        default     => ['bg-secondary text-white', $order['status']]
                                    };
                                ?>
                                <span class="badge rounded-pill px-3 py-2 <?= $statusBadge[0] ?>">
                                    <?= $statusBadge[1] ?>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-3 align-items-center">
                            <img src="<?= htmlspecialchars($order['product_image']) ?>" class="rounded-3" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='https://placehold.co/80x80?text=No+Img'">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($order['product_title']) ?></h6>
                                <div class="text-muted small">Người bán: <strong><?= htmlspecialchars($order['seller_name']) ?></strong> (SĐT: <?= htmlspecialchars($order['seller_phone'] ?? '---') ?>)</div>
                                <div class="text-danger fw-bold fs-5 mt-1"><?= number_format($order['total_price'], 0, ',', '.') ?> đ</div>
                            </div>
                            <div class="d-flex flex-column gap-2 text-end">
                                <a href="<?= BASE_URL ?>/orders/detail/<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    Chi tiết đơn
                                </a>
                                <?php if ($order['status'] === 'completed' && empty($order['review_id'])): ?>
                                    <button class="btn btn-warning btn-sm rounded-pill text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#reviewModal<?= $order['id'] ?>">
                                        <i class="bi bi-star-fill me-1"></i> Đánh giá
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Đánh giá người bán -->
                <?php if ($order['status'] === 'completed' && empty($order['review_id'])): ?>
                <div class="modal fade" id="reviewModal<?= $order['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold">Đánh giá người bán: <?= htmlspecialchars($order['seller_name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="<?= BASE_URL ?>/reviews/store" method="POST">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <div class="modal-body">
                                    <div class="mb-3 text-center">
                                        <label class="form-label fw-bold d-block">Mức độ hài lòng</label>
                                        <div class="rating-stars fs-2 text-warning cursor-pointer">
                                            <select name="rating" class="form-select w-50 mx-auto text-center fw-bold">
                                                <option value="5">⭐⭐⭐⭐⭐ (5 sao - Tuyệt vời)</option>
                                                <option value="4">⭐⭐⭐⭐ (4 sao - Tốt)</option>
                                                <option value="3">⭐⭐⭐ (3 sao - Bình thường)</option>
                                                <option value="2">⭐⭐ (2 sao - Chưa ưng ý)</option>
                                                <option value="1">⭐ (1 sao - Kém)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nhận xét chi tiết</label>
                                        <textarea name="comment" class="form-control" rows="3" placeholder="Đồ cũ đúng mô tả không? Người bán thân thiện không?"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-primary fw-bold">Gửi đánh giá</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

