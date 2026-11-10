<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-box-arrow-in-right fs-2"></i>
                    </div>
                    <h3 class="fw-bold">Đăng Nhập</h3>
                    <p class="text-muted small">Chào mừng bạn quay lại Sàn đồ cũ sinh viên</p>
                </div>

                <form action="<?= BASE_URL ?>/login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required value="seller@marketplace.test">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label fw-semibold mb-0">Mật khẩu</label>
                            <span class="text-muted small">Mẫu: 123456</span>
                        </div>
                        <div class="input-group mt-1">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required value="123456">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-semibold py-2 rounded-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Đăng Nhập
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted mb-0">Chưa có tài khoản? 
                        <a href="<?= BASE_URL ?>/register" class="fw-bold text-primary text-decoration-none">Đăng ký ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

