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
        $activePartnerId = filter_var($_GET['partner_id'] ?? null, FILTER_VALIDATE_INT);
        $activeProductId = filter_var($_GET['product_id'] ?? null, FILTER_VALIDATE_INT);
        $activeProductId = $activeProductId && $activeProductId > 0 ? $activeProductId : null;
        $activePartner = null;
        $activeProduct = null;

        if ($activePartnerId && $this->isValidConversation((int)$user['id'], $activePartnerId, $activeProductId)) {
            $activePartner = $this->userModel->find($activePartnerId);
            $activeProduct = $activeProductId ? $this->productModel->find($activeProductId) : null;
        }

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

        $partnerId = filter_var($_GET['partner_id'] ?? null, FILTER_VALIDATE_INT);
        $productId = !empty($_GET['product_id']) ? filter_var($_GET['product_id'], FILTER_VALIDATE_INT) : null;

        if (!$partnerId || $partnerId <= 0 || $partnerId === (int)$user['id']
            || ($productId !== null && (!$productId || $productId <= 0))) {
            $this->json(['error' => 'Đối tác không hợp lệ'], 400);
        }

        if (!$this->isValidConversation((int)$user['id'], $partnerId, $productId)) {
            $this->json(['error' => 'Cuộc trò chuyện hoặc sản phẩm không hợp lệ'], 404);
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

        $receiverId = filter_var($_POST['receiver_id'] ?? null, FILTER_VALIDATE_INT);
        $productId = !empty($_POST['product_id']) ? filter_var($_POST['product_id'], FILTER_VALIDATE_INT) : null;
        $messageText = trim($_POST['message'] ?? '');

        if (!$receiverId || $receiverId <= 0 || $receiverId === (int)$user['id']
            || $messageText === '' || strlen($messageText) > 8000
            || ($productId !== null && (!$productId || $productId <= 0))) {
            $this->json(['error' => 'Đối tác hoặc nội dung tin nhắn không hợp lệ'], 400);
        }

        if (!$this->isValidConversation((int)$user['id'], $receiverId, $productId)) {
            $this->json(['error' => 'Cuộc trò chuyện hoặc sản phẩm không hợp lệ'], 404);
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

    private function isValidConversation(int $userId, int $partnerId, ?int $productId): bool {
        if ($partnerId <= 0 || $partnerId === $userId || !$this->userModel->find($partnerId)) {
            return false;
        }

        if ($productId === null) {
            return true;
        }

        $product = $this->productModel->find($productId);
        return $product !== null
            && ((int)$product['user_id'] === $userId || (int)$product['user_id'] === $partnerId);
    }
}
