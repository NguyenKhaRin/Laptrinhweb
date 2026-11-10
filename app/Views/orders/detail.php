<?php
$customCss = ['orders'];
$customJs  = ['orders'];
?>
<div class="container my-5">
    <div class="mb-4">
        <a href="<?= $isBuyer ? BASE_URL . '/orders/buyer' : BASE_URL . '/orders/seller' ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách đơn hàng
        </a>
        <h3 class="fw-bold mt-2">Chi tiết đơn hàng #<?= htmlspecialchars($order['order_code']) ?></h3>
    </div>

    <div class="row g-4">
        <!-- Thông tin sản phẩm & thanh toán -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Sản phẩm giao dịch</h5>
                <div class="d-flex gap-3 align-items-center">
                    <img src="<?= htmlspecialchars($order['product_image']) ?>" class="rounded-3" style="width: 100px; height: 100px; object-fit: cover;" onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">
                            <a href="<?= BASE_URL ?>/products/detail/<?= $order['product_id'] ?>" class="text-dark text-decoration-none hover-primary">
                                <?= htmlspecialchars($order['product_title']) ?>
                            </a>
                        </h5>
                        <div class="badge bg-secondary-subtle text-secondary mb-2">
                            Tình trạng: <?= htmlspecialchars($order['product_condition'] ?? 'Dùng tốt') ?>
                        </div>
                        <div class="text-danger fw-bold fs-4">
                            <?= number_format($order['total_price'], 0, ',', '.') ?> đ
                        </div>
                    </div>
                </div>
            </div>

            <!-- Địa chỉ giao dịch & Ghi chú -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Địa chỉ & Thông tin liên hệ nhận hàng</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Số điện thoại nhận:</label>
                        <div class="fw-bold"><?= htmlspecialchars($order['phone']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Địa chỉ giao hàng:</label>
                        <div class="fw-bold"><?= htmlspecialchars($order['shipping_address']) ?></div>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                        <div class="col-12">
                            <label class="text-muted small">Ghi chú kèm theo:</label>
                            <div class="p-2 bg-light rounded-3"><?= htmlspecialchars($order['notes']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Đánh giá nếu có -->
            <?php if (!empty($order['review_rating'])): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-warning bg-opacity-10 border border-warning">
                    <h5 class="fw-bold mb-2 text-warning-emphasis"><i class="bi bi-star-fill text-warning me-2"></i>Đánh giá từ người mua</h5>
                    <div class="text-warning mb-2">
                        <?= str_repeat('⭐', $order['review_rating']) ?> (<?= $order['review_rating'] ?>/5 sao)
                    </div>
                    <p class="mb-0 text-dark"><?= htmlspecialchars($order['review_comment'] ?? 'Người mua không để lại bình luận.') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Trạng thái & Thao tác xử lý -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 80px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Trạng thái đơn hàng</h5>

                <div class="text-center py-3 mb-3">
                    <?php
                        $statusBadge = match($order['status']) {
                            'pending'   => ['bg-warning text-dark', 'Chờ xác nhận', 'bi-hourglass-split'],
                            'confirmed' => ['bg-info text-dark', 'Đã xác nhận', 'bi-check2-circle'],
                            'shipping'  => ['bg-primary text-white', 'Đang giao dịch', 'bi-truck'],
                            'completed' => ['bg-success text-white', 'Giao dịch thành công', 'bi-patch-check-fill'],
                            'cancelled' => ['bg-danger text-white', 'Đã hủy', 'bi-x-circle-fill'],
                            default     => ['bg-secondary text-white', $order['status'], 'bi-question-circle']
                        };
                    ?>
                    <div class="fs-1 text-primary mb-2">
                        <i class="bi <?= $statusBadge[2] ?>"></i>
                    </div>
                    <span class="badge rounded-pill px-3 py-2 fs-6 <?= $statusBadge[0] ?>">
                        <?= $statusBadge[1] ?>
                    </span>
                    <div class="text-muted small mt-2">
                        Thời gian tạo: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                    </div>
                </div>

                <!-- Thao tác thay đổi trạng thái -->
                <form action="<?= BASE_URL ?>/orders/update-status" method="POST" class="d-grid gap-2">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <?php if ($isSeller): ?>
                        <div class="small fw-semibold text-muted mb-1">Cập nhật tiến độ người bán:</div>
                        <?php if ($order['status'] === 'pending'): ?>
                            <button type="submit" name="status" value="confirmed" class="btn btn-success fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Xác nhận đơn hàng
                            </button>
                            <button type="submit" name="status" value="cancelled" class="btn btn-outline-danger btn-sm">
                                Từ chối / Hủy đơn
                            </button>
                        <?php elseif ($order['status'] === 'confirmed'): ?>
                            <button type="submit" name="status" value="shipping" class="btn btn-primary fw-bold">
                                <i class="bi bi-truck me-1"></i> Bắt đầu giao / Hẹn gặp
                            </button>
                            <button type="submit" name="status" value="cancelled" class="btn btn-outline-danger btn-sm">
                                Hủy đơn
                            </button>
                        <?php elseif ($order['status'] === 'shipping'): ?>
                            <button type="submit" name="status" value="completed" class="btn btn-success fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> Hoàn tất giao dịch
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($isBuyer && in_array($order['status'], ['pending', 'confirmed'])): ?>
                        <button type="submit" name="status" value="cancelled" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                            <i class="bi bi-x-circle me-1"></i> Hủy đơn hàng này
                        </button>
                    <?php endif; ?>
                </form>

                <hr>

                <!-- Liên hệ đối phương -->
                <div class="small">
                    <?php if ($isBuyer): ?>
                        <div class="fw-bold mb-1">Người bán: <?= htmlspecialchars($order['seller_name']) ?></div>
                        <div class="text-muted mb-2">SĐT: <?= htmlspecialchars($order['seller_phone']) ?></div>
                        <a href="<?= BASE_URL ?>/chat?partner_id=<?= $order['seller_id'] ?>&product_id=<?= $order['product_id'] ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                            <i class="bi bi-chat-dots-fill me-1"></i> Nhắn tin với người bán
                        </a>
                    <?php else: ?>
                        <div class="fw-bold mb-1">Người mua: <?= htmlspecialchars($order['buyer_name']) ?></div>
                        <div class="text-muted mb-2">SĐT: <?= htmlspecialchars($order['buyer_phone']) ?></div>
                        <a href="<?= BASE_URL ?>/chat?partner_id=<?= $order['buyer_id'] ?>&product_id=<?= $order['product_id'] ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                            <i class="bi bi-chat-dots-fill me-1"></i> Nhắn tin với người mua
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

