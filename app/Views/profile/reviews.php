<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="<?= BASE_URL ?>/profile/seller/<?= $seller['id'] ?>" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại trang người bán
                </a>
                <h3 class="fw-bold mt-2">Đánh giá uy tín: <?= htmlspecialchars($seller['full_name']) ?></h3>
                <div class="text-warning fs-5">
                    <i class="bi bi-star-fill"></i> <?= $seller['rating_avg'] ?? '5.0' ?> / 5.0
                    <span class="text-muted small fs-6">(Tổng <?= count($reviews) ?> đánh giá từ người mua)</span>
                </div>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <i class="bi bi-chat-square-quote text-muted display-4 mb-3"></i>
                    <h5>Chưa có đánh giá nào</h5>
                    <p class="text-muted mb-0">Người bán này chưa nhận được phản hồi sau mua hàng.</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($reviews as $r): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-light text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <?= mb_strtoupper(mb_substr($r['reviewer_name'], 0, 1)) ?>
                                    </div>
                                    <span class="fw-bold"><?= htmlspecialchars($r['reviewer_name']) ?></span>
                                </div>
                                <span class="text-warning"><?= str_repeat('⭐', $r['rating']) ?></span>
                            </div>

                            <div class="text-muted small mb-2">
                                Sản phẩm đã mua: <strong><?= htmlspecialchars($r['product_title']) ?></strong>
                            </div>

                            <p class="mb-2 text-secondary"><?= htmlspecialchars($r['comment'] ?? 'Người mua không để lại nhận xét.') ?></p>
                            <small class="text-muted" style="font-size: 11px;"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

