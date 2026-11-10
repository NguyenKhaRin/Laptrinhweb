<?php
$currentUser = $_SESSION['user'] ?? null;
$customCss = ['products'];
?>
<div class="container my-5">
    <div class="row g-4">
        <!-- Thông tin người bán -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center sticky-top" style="top: 80px;">
                <div class="avatar-circle bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                    <?= mb_strtoupper(mb_substr($seller['full_name'], 0, 1)) ?>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($seller['full_name']) ?></h4>
                <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($seller['address'] ?? 'Khu vực chưa cập nhật') ?></div>

                <div class="d-inline-flex align-items-center gap-1 bg-warning bg-opacity-25 text-warning-emphasis px-3 py-1 rounded-pill fw-bold mb-3">
                    <i class="bi bi-star-fill text-warning"></i>
                    <span><?= $seller['rating_avg'] ?? '5.0' ?> / 5.0</span>
                    <span class="text-muted small fw-normal">(<?= $seller['rating_count'] ?? 0 ?> đánh giá)</span>
                </div>

                <div class="border-top pt-3 text-start small">
                    <div class="mb-2"><i class="bi bi-calendar3 me-2 text-primary"></i>Tham gia: <?= date('d/m/Y', strtotime($seller['created_at'])) ?></div>
                    <div class="mb-2"><i class="bi bi-box-seam me-2 text-primary"></i>Đang bán: <?= count($products) ?> món đồ</div>
                    <div><i class="bi bi-telephone me-2 text-primary"></i>Liên hệ: <?= htmlspecialchars($seller['phone'] ?? 'Chưa cập nhật') ?></div>
                </div>

                <?php if ($currentUser && $currentUser['id'] != $seller['id']): ?>
                    <div class="mt-4">
                        <a href="<?= BASE_URL ?>/chat?partner_id=<?= $seller['id'] ?>" class="btn btn-primary w-100 rounded-pill fw-semibold">
                            <i class="bi bi-chat-dots-fill me-1"></i> Nhắn tin cho người bán
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Danh sách đồ cũ đang bán & Đánh giá -->
        <div class="col-lg-8">
            <ul class="nav nav-pills mb-4 bg-white p-2 rounded-4 shadow-sm" id="profileTab" role="tablist">
                <li class="nav-item flex-fill text-center">
                    <button class="nav-link active fw-bold w-100 rounded-pill" id="products-tab" data-bs-toggle="pill" data-bs-target="#productsPane">
                        <i class="bi bi-grid me-1"></i> Sản phẩm đang bán (<?= count($products) ?>)
                    </button>
                </li>
                <li class="nav-item flex-fill text-center">
                    <button class="nav-link fw-bold w-100 rounded-pill" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviewsPane">
                        <i class="bi bi-star me-1"></i> Đánh giá từ người mua (<?= count($reviews) ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab Đồ đang bán -->
                <div class="tab-pane fade show active" id="productsPane">
                    <?php if (empty($products)): ?>
                        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                            <p class="text-muted mb-0">Người bán hiện không có sản phẩm nào đang rao.</p>
                        </div>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-sm-2 g-3">
                            <?php foreach ($products as $p): ?>
                                <div class="col">
                                    <div class="card h-100 product-card border-0 shadow-sm rounded-4 overflow-hidden">
                                        <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>">
                                            <div class="product-img-wrapper" style="height: 180px;">
                                                <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['title']) ?>" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                            </div>
                                        </a>
                                        <div class="card-body p-3">
                                            <h6 class="card-title fw-bold text-truncate mb-1">
                                                <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>" class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($p['title']) ?>
                                                </a>
                                            </h6>
                                            <div class="text-danger fw-bold"><?= number_format($p['price'], 0, ',', '.') ?> đ</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Đánh giá -->
                <div class="tab-pane fade" id="reviewsPane">
                    <?php if (empty($reviews)): ?>
                        <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                            <p class="text-muted mb-0">Chưa có đánh giá nào cho người bán này.</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($reviews as $r): ?>
                                <div class="card border-0 shadow-sm rounded-4 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold"><?= htmlspecialchars($r['reviewer_name']) ?></div>
                                        <span class="text-warning small"><?= str_repeat('⭐', $r['rating']) ?></span>
                                    </div>
                                    <p class="text-secondary small mb-1"><?= htmlspecialchars($r['comment'] ?? 'Người mua không để lại bình luận.') ?></p>
                                    <small class="text-muted" style="font-size: 11px;"><?= date('d/m/Y', strtotime($r['created_at'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

