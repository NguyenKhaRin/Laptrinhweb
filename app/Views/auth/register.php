<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex p-3 mb-2">
                        <i class="bi bi-person-plus fs-2"></i>
                    </div>
                    <h3 class="fw-bold">Đăng Ký Tài Khoản</h3>
                    <p class="text-muted small">Mua bán & thanh lý đồ cũ nhanh chóng, an toàn</p>
                </div>

                <form action="<?= BASE_URL ?>/register" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Nguyễn Văn A" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="student@university.edu.vn" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Tối thiểu 6 ký tự" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label fw-semibold">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="09xxxxxxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label fw-semibold">Khu vực / Ký túc xá</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="KTX Khu B, Cầu Giấy...">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-semibold py-2 rounded-3">
                            <i class="bi bi-person-check me-1"></i> Hoàn tất Đăng Ký
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="text-muted mb-0">Đã có tài khoản? 
                        <a href="<?= BASE_URL ?>/login" class="fw-bold text-primary text-decoration-none">Đăng nhập tại đây</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

