<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= BASE_URL ?>/admin" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Quay lại Dashboard</a>
            <h3 class="fw-bold mt-1"><i class="bi bi-box-seam-fill text-warning me-2"></i>Quản Lý Bài Đăng Đồ Cũ</h3>
        </div>
        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">Tổng: <?= count($products) ?> bài đăng</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Người đăng bán</th>
                        <th>Giá bán</th>
                        <th>Tình trạng</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= htmlspecialchars($p['image']) ?>" class="rounded-2" style="width: 48px; height: 48px; object-fit: cover;" onerror="this.src='https://placehold.co/48x48?text=No+Img'">
                                    <div>
                                        <div class="fw-bold text-truncate" style="max-width: 250px;">
                                            <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>" class="text-dark text-decoration-none" target="_blank">
                                                <?= htmlspecialchars($p['title']) ?>
                                            </a>
                                        </div>
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['location']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name']) ?></span></td>
                            <td><?= htmlspecialchars($p['seller_name']) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($p['price'], 0, ',', '.') ?> đ</td>
                            <td><small class="text-muted"><?= htmlspecialchars($p['condition']) ?></small></td>
                            <td>
                                <?php
                                    $statusBadge = match($p['status']) {
                                        'available' => 'bg-success text-white',
                                        'reserved'  => 'bg-warning text-dark',
                                        'sold'      => 'bg-secondary text-white',
                                        default     => 'bg-dark text-white'
                                    };
                                ?>
                                <span class="badge rounded-pill px-2 py-1 <?= $statusBadge ?>">
                                    <?= $p['status'] ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-2" target="_blank" title="Xem bài">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="<?= BASE_URL ?>/admin/products/delete/<?= $p['id'] ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này không?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Gỡ bài">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
