<footer class="bg-white border-top mt-auto py-4">
    <div class="container">
        <div class="row gy-3 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <span class="fw-bold text-primary">Secondhand<span class="text-warning">Hub</span></span>
                <span class="text-muted ms-2">© <?= date('Y') ?> Sàn giao dịch đồ cũ sinh viên. Nhóm phát triển MVC thuần PHP.</span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="<?= BASE_URL ?>/" class="text-muted text-decoration-none me-3 hover-primary">Trang chủ</a>
                <a href="<?= BASE_URL ?>/products/create" class="text-muted text-decoration-none me-3 hover-primary">Đăng tin</a>
                <a href="<?= BASE_URL ?>/orders/buyer" class="text-muted text-decoration-none hover-primary">Đơn hàng</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS chung cho toàn hệ thống -->
<script src="<?= ASSET_URL ?>/js/main.js"></script>

<!-- JS riêng cho từng module theo yêu cầu trang -->
<?php if (!empty($customJs)): ?>
    <?php foreach ((array)$customJs as $js): ?>
        <script src="<?= ASSET_URL ?>/js/<?= htmlspecialchars($js) ?>.js"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

