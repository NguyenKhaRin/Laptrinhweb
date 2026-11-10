<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= BASE_URL ?>/admin" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Quay lại Dashboard</a>
            <h3 class="fw-bold mt-1"><i class="bi bi-people-fill text-primary me-2"></i>Quản Lý Người Dùng & Phân Quyền</h3>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">Tổng: <?= count($users) ?> tài khoản</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Họ tên & Email</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ / Khu vực</th>
                        <th>Vai trò (Phân quyền)</th>
                        <th>Đánh giá</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="ps-4 fw-bold">#<?= $u['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                        <?= mb_strtoupper(mb_substr($u['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($u['full_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['phone'] ?? 'Chưa có') ?></td>
                            <td><?= htmlspecialchars($u['address'] ?? 'Chưa có') ?></td>
                            <td>
                                <?php
                                    $roleBadge = match($u['role']) {
                                        'admin'  => 'bg-danger text-white',
                                        'seller' => 'bg-success text-white',
                                        default  => 'bg-secondary text-white'
                                    };
                                ?>
                                <span class="badge rounded-pill px-3 py-2 <?= $roleBadge ?>">
                                    <?= strtoupper($u['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-warning small"><i class="bi bi-star-fill"></i> <?= $u['rating_avg'] ?></span>
                                <small class="text-muted">(<?= $u['rating_count'] ?>)</small>
                            </td>
                            <td class="text-end pe-4">
                                <!-- Form đổi vai trò trực tiếp -->
                                <form action="<?= BASE_URL ?>/admin/users/update-role" method="POST" class="d-inline-flex gap-1 align-items-center">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select name="role" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                        <option value="buyer" <?= $u['role'] === 'buyer' ? 'selected' : '' ?>>Buyer</option>
                                        <option value="seller" <?= $u['role'] === 'seller' ? 'selected' : '' ?>>Seller</option>
                                        <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
