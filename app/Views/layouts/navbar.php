<?php
$currentUser = $_SESSION['user'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm py-2">
    <div class="container">
        <!-- Logo thương hiệu -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
            <i class="bi bi-shop fs-3"></i>
            <span>Secondhand<span class="text-warning">Hub</span></span>
        </a>

        <!-- Nút toggle mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Thanh tìm kiếm nhanh -->
            <form class="d-flex mx-auto my-2 my-lg-0 col-12 col-lg-5" action="<?= BASE_URL ?>/" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="keyword" placeholder="Tìm đồ cũ: laptop, điện thoại, sách..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    <button class="btn btn-warning text-dark fw-semibold" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Menu bên phải -->
            <ul class="navbar-nav ms-auto align-items-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link text-white fw-medium" href="<?= BASE_URL ?>/">
                        <i class="bi bi-compass me-1"></i> Khám phá
                    </a>
                </li>

                <?php if ($currentUser): ?>
                    <!-- Nút nhắn tin -->
                    <li class="nav-item">
                        <a class="nav-link text-white position-relative" href="<?= BASE_URL ?>/chat">
                            <i class="bi bi-chat-dots-fill fs-5"></i>
                            <span class="visually-hidden">Tin nhắn</span>
                        </a>
                    </li>

                    <!-- Quản lý đơn mua / bán -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-medium" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bag-check me-1"></i> Đơn hàng
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/orders/buyer"><i class="bi bi-cart3 me-2 text-primary"></i>Đơn tôi đã mua</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/orders/seller"><i class="bi bi-shop-window me-2 text-success"></i>Đơn tôi bán</a></li>
                        </ul>
                    </li>

                    <!-- Nút Đăng tin bán -->
                    <li class="nav-item my-2 my-lg-0">
                        <a class="btn btn-warning fw-bold text-dark px-3 rounded-pill shadow-sm" href="<?= BASE_URL ?>/products/create">
                            <i class="bi bi-plus-circle-fill me-1"></i> Đăng Bán
                        </a>
                    </li>

                    <!-- Dropdown thông tin tài khoản -->
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle bg-light text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1)) ?>
                            </div>
                            <span class="fw-semibold d-none d-xl-inline"><?= htmlspecialchars($currentUser['full_name']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold"><?= htmlspecialchars($currentUser['full_name']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($currentUser['email']) ?></div>
                            </li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile/seller/<?= $currentUser['id'] ?>"><i class="bi bi-person me-2"></i>Trang cá nhân</a></li>
                            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                                <li><a class="dropdown-item text-danger fw-bold" href="<?= BASE_URL ?>/admin"><i class="bi bi-speedometer2 me-2"></i>Bảng Quản Trị (Admin)</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-semibold" href="<?= BASE_URL ?>/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light text-primary fw-bold px-3 rounded-pill" href="<?= BASE_URL ?>/register">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Vùng hiển thị thông báo flash message -->
<div class="container mt-3">
    <?php if (!empty($_SESSION['flash']['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['flash']['success']) ?>
            <?php unset($_SESSION['flash']['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash']['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['flash']['error']) ?>
            <?php unset($_SESSION['flash']['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

