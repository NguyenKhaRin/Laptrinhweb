<div class="container my-5">
    <!-- Header Admin -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-speedometer2 text-danger me-2"></i>Bảng Điều Khiển Quản Trị (Admin)</h2>
            <p class="text-muted mb-0">Quản lý toàn bộ người dùng, đồ cũ thanh lý và giao dịch trên sàn</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-people me-1"></i> Quản lý User
            </a>
            <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-success rounded-pill">
                <i class="bi bi-box-seam me-1"></i> Quản lý Bài Đăng
            </a>
        </div>
    </div>

    <!-- 4 Thẻ Thống Kê (KPI Cards) -->
    <div class="row g-3 mb-4">
        <!-- Người dùng -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Người dùng</span>
                        <h3 class="fw-bold mb-0 mt-1"><?= $totalUsers ?></h3>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm đăng bán -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Bài đăng đồ cũ</span>
                        <h3 class="fw-bold mb-0 mt-1"><?= $totalProducts ?></h3>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-tags fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Tổng đơn hàng</span>
                        <h3 class="fw-bold mb-0 mt-1"><?= $totalOrders ?></h3>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle">
                        <i class="bi bi-cart-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giá trị giao dịch thành công -->
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold text-uppercase">Giao dịch thành công</span>
                        <h4 class="fw-bold mb-0 mt-1 text-success"><?= number_format($totalRevenue, 0, ',', '.') ?> đ</h4>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Đơn hàng mới nhất -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-receipt text-primary me-2"></i>Đơn hàng gần đây</span>
                    <a href="<?= BASE_URL ?>/orders/seller" class="small text-decoration-none">Xem tất cả</a>
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã đơn</th>
                                <th>Người mua</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Chưa có đơn hàng nào</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $ro): ?>
                                    <tr>
                                        <td class="fw-semibold">#<?= htmlspecialchars($ro['order_code']) ?></td>
                                        <td><?= htmlspecialchars($ro['buyer_name']) ?></td>
                                        <td class="text-danger fw-bold"><?= number_format($ro['total_price'], 0, ',', '.') ?> đ</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                                <?= $ro['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bài đăng đồ cũ mới nhất -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-box-seam text-warning me-2"></i>Bài đăng mới lên sàn</span>
                    <a href="<?= BASE_URL ?>/admin/products" class="small text-decoration-none">Xem tất cả</a>
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Người bán</th>
                                <th>Giá thanh lý</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentProducts)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Chưa có bài đăng nào</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentProducts as $rp): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-truncate" style="max-width: 180px;">
                                                <a href="<?= BASE_URL ?>/products/detail/<?= $rp['id'] ?>" class="text-dark text-decoration-none" target="_blank">
                                                    <?= htmlspecialchars($rp['title']) ?>
                                                </a>
                                            </div>
                                            <small class="text-muted"><?= htmlspecialchars($rp['category_name']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($rp['seller_name']) ?></td>
                                        <td class="text-danger fw-bold"><?= number_format($rp['price'], 0, ',', '.') ?> đ</td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/products/detail/<?= $rp['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
