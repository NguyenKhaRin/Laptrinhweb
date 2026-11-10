<?php
$currentUser = $_SESSION['user'] ?? null;
$customCss = ['chat'];
$customJs  = ['chat'];
?>
<div class="container my-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden chat-container-card">
        <div class="row g-0 h-100">
            <!-- Cột trái: Danh sách cuộc trò chuyện -->
            <div class="col-md-4 col-lg-3 border-end bg-white d-flex flex-column conversation-sidebar">
                <div class="p-3 border-bottom bg-light">
                    <h5 class="fw-bold mb-0"><i class="bi bi-chat-text-fill text-primary me-2"></i>Tin nhắn</h5>
                </div>

                <div class="overflow-auto conversation-list flex-grow-1" id="conversationList">
                    <?php if (empty($conversations)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-chat-square-dots fs-1 d-block mb-2"></i>
                            <p class="small mb-0">Chưa có cuộc trò chuyện nào.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($conversations as $c): ?>
                            <a href="<?= BASE_URL ?>/chat?partner_id=<?= $c['partner_id'] ?><?= !empty($c['product_id']) ? '&product_id='.$c['product_id'] : '' ?>" 
                               class="list-group-item list-group-item-action p-3 border-bottom d-flex align-items-center gap-3 <?= (!empty($activePartner) && $activePartner['id'] == $c['partner_id']) ? 'active-chat' : '' ?>">
                                <div class="avatar-circle bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                    <?= mb_strtoupper(mb_substr($c['partner_name'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-bold text-truncate"><?= htmlspecialchars($c['partner_name']) ?></div>
                                        <small class="text-muted" style="font-size: 11px;"><?= date('H:i', strtotime($c['last_time'])) ?></small>
                                    </div>
                                    <?php if (!empty($c['product_title'])): ?>
                                        <div class="badge bg-secondary-subtle text-secondary small text-truncate d-inline-block max-w-100">
                                            <?= htmlspecialchars($c['product_title']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-muted small text-truncate"><?= htmlspecialchars($c['last_message']) ?></div>
                                </div>
                                <?php if (!empty($c['unread_count']) && $c['unread_count'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill"><?= $c['unread_count'] ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: Khung chat chính -->
            <div class="col-md-8 col-lg-9 d-flex flex-column bg-light chat-main-area">
                <?php if ($activePartner): ?>
                    <!-- Tiêu đề Chat & Người đang trò chuyện -->
                    <div class="p-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                <?= mb_strtoupper(mb_substr($activePartner['full_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($activePartner['full_name']) ?></h6>
                                <small class="text-success"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Đang hoạt động</small>
                            </div>
                        </div>

                        <?php if ($activeProduct): ?>
                            <!-- Thẻ ghim sản phẩm đang bàn luận -->
                            <div class="d-flex align-items-center gap-2 p-2 bg-light rounded-3 border">
                                <img src="<?= htmlspecialchars($activeProduct['image']) ?>" class="rounded-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold small text-truncate" style="max-width: 180px;"><?= htmlspecialchars($activeProduct['title']) ?></div>
                                    <div class="text-danger small fw-bold"><?= number_format($activeProduct['price'], 0, ',', '.') ?> đ</div>
                                </div>
                                <a href="<?= BASE_URL ?>/products/detail/<?= $activeProduct['id'] ?>" class="btn btn-sm btn-outline-primary ms-2" target="_blank">Xem</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Khung hiển thị nội dung tin nhắn -->
                    <div class="p-3 overflow-auto flex-grow-1 message-box" id="messageBox" 
                         data-partner-id="<?= $activePartner['id'] ?>" 
                         data-product-id="<?= $activeProduct['id'] ?? '' ?>"
                         data-base-url="<?= BASE_URL ?>">
                        <div class="text-center text-muted py-4 loading-chat">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Đang tải tin nhắn...
                        </div>
                    </div>

                    <!-- Form nhập và gửi tin nhắn -->
                    <div class="p-3 bg-white border-top">
                        <form id="chatForm" class="d-flex gap-2">
                            <input type="hidden" name="receiver_id" value="<?= $activePartner['id'] ?>">
                            <input type="hidden" name="product_id" value="<?= $activeProduct['id'] ?? '' ?>">
                            <input type="text" class="form-control rounded-pill" id="messageInput" name="message" placeholder="Nhập tin nhắn..." autocomplete="off" required>
                            <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted p-5">
                        <i class="bi bi-chat-heart text-primary opacity-50 display-1 mb-3"></i>
                        <h5>Chọn một cuộc trò chuyện để bắt đầu nhắn tin</h5>
                        <p class="small">Bạn có thể hỏi người bán về tình trạng đồ cũ, giá cả và hẹn địa điểm giao dịch.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

