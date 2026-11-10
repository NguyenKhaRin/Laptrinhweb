<?php
$customCss = ['orders'];
$customJs  = ['orders'];
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-shop-window text-success me-2"></i>Quản Lý Đơn Bán Hàng</h3>
            <p class="text-muted mb-0">Xác nhận và cập nhật tiến độ giao dịch đồ cũ cho người mua</p>
        </div>
        <a href="<?= BASE_URL ?>/products/create" class="btn btn-warning fw-bold text-dark rounded-pill">
            <i class="bi bi-plus-circle me-1"></i> Đăng thêm đồ cũ
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
            <i class="bi bi-receipt text-muted display-3 mb-3"></i>
            <h5>Chưa có người đặt mua sản phẩm của bạn!</h5>
            <p class="text-muted">Đăng bán các món đồ sinh viên không dùng đến để tiếp cận người mua nhanh hơn.</p>
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
                            <div class="d-flex align-items-center gap-2">
                                <?php
                                    $statusBadge = match($order['status']) {
                                        'pending'   => ['bg-warning text-dark', 'Chờ bạn xác nhận'],
                                        'confirmed' => ['bg-info text-dark', 'Đã xác nhận'],
                                        'shipping'  => ['bg-primary text-white', 'Đang giao dịch'],
                                        'completed' => ['bg-success text-white', 'Đã hoàn tất'],
                                        'cancelled' => ['bg-danger text-white', 'Đã hủy'],
                                        default     => ['bg-secondary text-white', $order['status']]
                                    };
                                ?>
                                <span class="badge rounded-pill px-3 py-2 <?= $statusBadge[0] ?>">
                                    <?= $statusBadge[1] ?>
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center g-3">
                            <div class="col-md-7 d-flex gap-3 align-items-center">
                                <img src="<?= htmlspecialchars($order['product_image']) ?>" class="rounded-3" style="width: 75px; height: 75px; object-fit: cover;" onerror="this.src='https://placehold.co/75x75?text=No+Img'">
                                <div>
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($order['product_title']) ?></h6>
                                    <div class="small text-muted">Người mua: <strong><?= htmlspecialchars($order['buyer_name']) ?></strong> (<?= htmlspecialchars($order['buyer_phone']) ?>)</div>
                                    <div class="text-danger fw-bold fs-5 mt-1"><?= number_format($order['total_price'], 0, ',', '.') ?> đ</div>
                                </div>
                            </div>

                            <div class="col-md-5 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <a href="<?= BASE_URL ?>/orders/detail/<?= $order['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                        Xem chi tiết
                                    </a>

                                    <!-- Nút đổi trạng thái nhanh bằng AJAX -->
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-success btn-sm rounded-pill px-3 btn-change-status" data-order-id="<?= $order['id'] ?>" data-status="confirmed">
                                            <i class="bi bi-check-lg me-1"></i> Xác nhận đơn
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm rounded-pill px-2 btn-change-status" data-order-id="<?= $order['id'] ?>" data-status="cancelled">
                                            Hủy
                                        </button>
                                    <?php elseif ($order['status'] === 'confirmed'): ?>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 btn-change-status" data-order-id="<?= $order['id'] ?>" data-status="shipping">
                                            <i class="bi bi-truck me-1"></i> Bắt đầu giao / Hẹn gặp
                                        </button>
                                    <?php elseif ($order['status'] === 'shipping'): ?>
                                        <button class="btn btn-success btn-sm rounded-pill px-3 btn-change-status" data-order-id="<?= $order['id'] ?>" data-status="completed">
                                            <i class="bi bi-check-circle-fill me-1"></i> Hoàn thành giao dịch
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

