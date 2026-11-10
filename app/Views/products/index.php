<?php
$customCss = ['products'];
$customJs  = ['products'];
?>
<div class="container my-4">
    <!-- Banner chào mừng -->
    <div class="p-4 p-md-5 mb-4 rounded-4 bg-primary text-white shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-2">Thanh lý & Trao đổi đồ cũ</span>
                <h1 class="display-6 fw-bold mb-2">Sàn Đồ Cũ Sinh Viên Tiện Lợi & Giá Rẻ</h1>
                <p class="lead mb-3">Tìm mua laptop, điện thoại, sách vở, đồ gia dụng với giá tiết kiệm nhất.</p>
                <a href="<?= BASE_URL ?>/products/create" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill">
                    <i class="bi bi-camera-fill me-1"></i> Đăng bán ngay (Miễn phí)
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Bộ lọc sản phẩm (Sidebar Filter) -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 sticky-top" style="top: 80px;">
                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-funnel text-primary me-2"></i> Bộ Lọc Tìm Kiếm
                </h5>

                <form id="filterForm" action="<?= BASE_URL ?>/" method="GET">
                    <!-- Danh mục -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Danh mục</label>
                        <select name="category_id" class="form-select rounded-3">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (!empty($filters['category_id']) && $filters['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tình trạng -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Tình trạng</label>
                        <select name="condition" class="form-select rounded-3">
                            <option value="">Tất cả tình trạng</option>
                            <option value="new" <?= ($filters['condition'] ?? '') === 'new' ? 'selected' : '' ?>>Mới 100%</option>
                            <option value="like_new" <?= ($filters['condition'] ?? '') === 'like_new' ? 'selected' : '' ?>>Như mới 99%</option>
                            <option value="good" <?= ($filters['condition'] ?? '') === 'good' ? 'selected' : '' ?>>Còn tốt (90%)</option>
                            <option value="fair" <?= ($filters['condition'] ?? '') === 'fair' ? 'selected' : '' ?>>Dùng ổn</option>
                        </select>
                    </div>

                    <!-- Khoảng giá -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Khoảng giá (VNĐ)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control" placeholder="Từ" value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control" placeholder="Đến" value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Sắp xếp -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Sắp xếp theo</label>
                        <select name="sort" class="form-select rounded-3">
                            <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                            <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                            <option value="popular" <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Xem nhiều nhất</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-3 fw-semibold">
                            <i class="bi bi-search me-1"></i> Áp dụng bộ lọc
                        </button>
                        <a href="<?= BASE_URL ?>/" class="btn btn-outline-secondary rounded-3 btn-sm">Đặt lại</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách sản phẩm (Product Grid) -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Tin đăng bán mới nhất (<?= count($products) ?>)</h4>
            </div>

            <?php if (empty($products)): ?>
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <i class="bi bi-inbox text-muted display-4 mb-3"></i>
                    <h5>Không tìm thấy sản phẩm nào!</h5>
                    <p class="text-muted">Hãy thử thay đổi từ khóa hoặc điều kiện lọc giá của bạn.</p>
                    <div>
                        <a href="<?= BASE_URL ?>/" class="btn btn-outline-primary rounded-pill">Xem tất cả sản phẩm</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="productGrid">
                    <?php foreach ($products as $p): ?>
                        <div class="col">
                            <div class="card h-100 product-card border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                                <!-- Badge tình trạng -->
                                <span class="badge bg-dark bg-opacity-75 position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1">
                                    <?php 
                                        echo match($p['condition']) {
                                            'new' => 'Mới 100%',
                                            'like_new' => 'Như mới 99%',
                                            'good' => 'Tốt 90%',
                                            default => 'Dùng ổn'
                                        };
                                    ?>
                                </span>

                                <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>" class="text-decoration-none">
                                    <div class="product-img-wrapper">
                                        <img src="<?= htmlspecialchars($p['image']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['title']) ?>" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                    </div>
                                </a>

                                <div class="card-body d-flex flex-column p-3">
                                    <div class="small text-muted mb-1 d-flex justify-content-between">
                                        <span><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['location']) ?></span>
                                        <span><i class="bi bi-eye me-1"></i><?= $p['view_count'] ?></span>
                                    </div>

                                    <h6 class="card-title fw-bold text-truncate-2 mb-2">
                                        <a href="<?= BASE_URL ?>/products/detail/<?= $p['id'] ?>" class="text-dark text-decoration-none hover-primary">
                                            <?= htmlspecialchars($p['title']) ?>
                                        </a>
                                    </h6>

                                    <div class="mt-auto pt-2 border-top">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <span class="text-danger fw-bold fs-5"><?= number_format($p['price'], 0, ',', '.') ?> đ</span>
                                            <?php if (!empty($p['original_price']) && $p['original_price'] > $p['price']): ?>
                                                <small class="text-muted text-decoration-line-through"><?= number_format($p['original_price'], 0, ',', '.') ?> đ</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="small text-muted mt-2 d-flex align-items-center justify-content-between">
                                            <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($p['seller_name'] ?? 'Người bán') ?></span>
                                            <span class="text-warning"><i class="bi bi-star-fill me-1"></i><?= $p['seller_rating'] ?? '5.0' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

