<?php
/**
 * ========================================================
 * CHAT CONTROLLER - GỬI/NHẬN TIN NHẮN (AJAX / LONG-POLLING)
 * Phụ trách bởi: Thành viên phụ trách Module Chat
 * ========================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Product;

class ChatController extends Controller {
    private Message $messageModel;
    private User $userModel;
    private Product $productModel;

    public function __construct() {
        $this->messageModel = new Message();
        $this->userModel = new User();
        $this->productModel = new Product();
    }

    /**
     * Màn hình Hộp thư tin nhắn (Chat Box)
     */
    public function inbox(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $conversations = $this->messageModel->getRecentConversations($user['id']);

        // Nếu có truyền đối tác qua URL query (?partner_id=...&product_id=...)
        $activePartnerId = isset($_GET['partner_id']) ? (int)$_GET['partner_id'] : null;
        $activeProductId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

        $activePartner = $activePartnerId ? $this->userModel->find($activePartnerId) : null;
        $activeProduct = $activeProductId ? $this->productModel->find($activeProductId) : null;

        $this->view('chat/inbox', [
            'pageTitle'       => 'Hộp thư trao đổi',
            'conversations'   => $conversations,
            'activePartner'   => $activePartner,
            'activeProduct'   => $activeProduct,
        ]);
    }

    /**
     * API: Lấy danh sách tin nhắn giữa 2 người (Fetch / AJAX Polling)
     */
    public function getMessages(): void {
        $user = $this->user();
        if (!$user) {
            $this->json(['error' => 'Chưa đăng nhập'], 401);
        }

        $partnerId = (int)($_GET['partner_id'] ?? 0);
        $productId = !empty($_GET['product_id']) ? (int)$_GET['product_id'] : null;

        if ($partnerId <= 0) {
            $this->json(['error' => 'Đối tác không hợp lệ'], 400);
        }

        // Đánh dấu là đã đọc
        $this->messageModel->markAsRead($partnerId, $user['id'], $productId);

        $messages = $this->messageModel->getConversation($user['id'], $partnerId, $productId);

        $this->json([
            'success'  => true,
            'messages' => $messages,
            'my_id'    => $user['id']
        ]);
    }

    /**
     * API: Gửi tin nhắn mới
     */
    public function sendMessage(): void {
        $user = $this->user();
        if (!$user) {
            $this->json(['error' => 'Chưa đăng nhập'], 401);
        }

        $receiverId = (int)($_POST['receiver_id'] ?? 0);
        $productId = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
        $messageText = trim($_POST['message'] ?? '');

        if ($receiverId <= 0 || empty($messageText)) {
            $this->json(['error' => 'Nội dung tin nhắn không được để trống'], 400);
        }

        $id = $this->messageModel->create([
            'sender_id'   => $user['id'],
            'receiver_id' => $receiverId,
            'product_id'  => $productId,
            'message'     => $messageText,
            'is_read'     => 0
        ]);

        $createdMessage = $this->messageModel->find($id);

        $this->json([
            'success' => true,
            'message' => $createdMessage,
            'my_id'   => $user['id']
        ]);
    }
}

