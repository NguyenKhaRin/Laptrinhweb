<?php
/**
 * ========================================================
 * PRODUCT CONTROLLER - ĐĂNG BÁN, SỬA/XÓA, CHI TIẾT & TÌM KIẾM
 * Phụ trách bởi: Thành viên phụ trách Module Sản phẩm
 * ========================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\User;
use DomainException;

class ProductController extends Controller {
    private Product $productModel;
    private User $userModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    /**
     * Danh sách sản phẩm (Trang chủ & Tìm kiếm/Lọc)
     */
    public function index(): void {
        $filters = [
            'keyword'     => trim($_GET['keyword'] ?? ''),
            'category_id' => $_GET['category_id'] ?? null,
            'min_price'   => $_GET['min_price'] ?? null,
            'max_price'   => $_GET['max_price'] ?? null,
            'condition'   => $_GET['condition'] ?? null,
            'sort'        => $_GET['sort'] ?? 'newest'
        ];

        // Lấy danh sách danh mục để đổ vào dropdown lọc
        $db = \App\Config\Database::getConnection();
        $categories = $db->query("SELECT * FROM `categories` ORDER BY `name` ASC")->fetchAll();

        // Lấy danh sách sản phẩm theo bộ lọc
        $products = $this->productModel->filter($filters);

        // Nếu là yêu cầu AJAX thì trả JSON để render danh sách nhanh
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->json(['success' => true, 'products' => $products]);
            return;
        }

        $this->view('products/index', [
            'pageTitle'  => 'Chợ Đồ Cũ - Tìm kiếm & Mua sắm',
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $filters
        ]);
    }

    /**
     * Chi tiết sản phẩm
     */
    public function detail(int|string $id): void {
        $id = (int)$id;
        $product = $this->productModel->getDetail($id);

        if (!$product) {
            $this->redirect('/');
        }

        // Tăng lượt xem
        $this->productModel->incrementViews($id);

        // Lấy các sản phẩm liên quan cùng danh mục
        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `products` WHERE `category_id` = :cid AND `id` != :pid AND `status` = 'available' LIMIT 4");
        $stmt->execute(['cid' => $product['category_id'], 'pid' => $id]);
        $relatedProducts = $stmt->fetchAll();

        $this->view('products/detail', [
            'pageTitle'       => $product['title'],
            'product'         => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }

    /**
     * Giao diện Đăng bán sản phẩm mới
     */
    public function create(): void {
        $db = \App\Config\Database::getConnection();
        $categories = $db->query("SELECT * FROM `categories` ORDER BY `name` ASC")->fetchAll();

        $this->view('products/create', [
            'pageTitle'  => 'Đăng bán đồ cũ',
            'categories' => $categories,
            'error'      => $this->flash('error')
        ]);
    }

    /**
     * Xử lý lưu sản phẩm mới
     */
    public function store(): void {
        $user = $this->user();
        if (!$user) {
            $this->redirect('/login');
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $originalPrice = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
        $condition = $_POST['condition'] ?? 'like_new';
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '' || strlen($title) > 255 || $categoryId <= 0 || $price <= 0
            || ($originalPrice !== null && $originalPrice <= 0)
            || !in_array($condition, ['new', 'like_new', 'good', 'fair'], true)
            || $location === '' || strlen($location) > 150 || $description === '') {
            $this->flash('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc.');
            $this->redirect('/products/create');
        }

        try {
            $imageUrl = $this->uploadProductImage($_FILES['image'] ?? []);
        } catch (DomainException $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/products/create');
        }

        $productId = $this->productModel->create([
            'user_id'        => $user['id'],
            'category_id'    => $categoryId,
            'title'          => $title,
            'description'    => $description,
            'price'          => $price,
            'original_price' => $originalPrice,
            'condition'      => $condition,
            'location'       => $location,
            'image'          => $imageUrl,
            'status'         => 'available',
            'view_count'     => 0
        ]);

        $this->flash('success', 'Đăng bán sản phẩm thành công!');
        $this->redirect('/products/detail/' . $productId);
    }

    /**
     * Xem hồ sơ và danh sách đồ cũ của người bán
     */
    public function sellerProfile(int|string $sellerId): void {
        $sellerId = (int)$sellerId;
        $seller = $this->userModel->find($sellerId);

        if (!$seller) {
            $this->redirect('/');
        }

        $products = $this->productModel->getBySeller($sellerId);

        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT r.*, u.full_name as reviewer_name, u.avatar as reviewer_avatar 
                             FROM `reviews` r 
                             JOIN `users` u ON r.reviewer_id = u.id 
                             WHERE r.reviewee_id = :sid 
                             ORDER BY r.id DESC");
        $stmt->execute(['sid' => $sellerId]);
        $reviews = $stmt->fetchAll();

        $this->view('profile/seller_profile', [
            'pageTitle' => 'Hồ sơ người bán: ' . $seller['full_name'],
            'seller'    => $seller,
            'products'  => $products,
            'reviews'   => $reviews
        ]);
    }

    /**
     * Giao diện chỉnh sửa thông tin bài đăng bán
     */
    public function edit(int|string $id): void {
        $user = $this->user();
        $id = (int)$id;
        $product = $this->productModel->find($id);

        if (!$product || ($product['user_id'] != $user['id'] && ($user['role'] ?? '') !== 'admin')) {
            $this->flash('error', 'Bạn không có quyền chỉnh sửa sản phẩm này.');
            $this->redirect('/');
        }

        $db = \App\Config\Database::getConnection();
        $categories = $db->query("SELECT * FROM `categories` ORDER BY `name` ASC")->fetchAll();

        $this->view('products/edit', [
            'pageTitle'  => 'Chỉnh sửa: ' . $product['title'],
            'product'    => $product,
            'categories' => $categories,
            'error'      => $this->flash('error')
        ]);
    }

    /**
     * Xử lý cập nhật thông tin sản phẩm
     */
    public function update(int|string $id): void {
        $user = $this->user();
        $id = (int)$id;
        $product = $this->productModel->find($id);

        if (!$product || ($product['user_id'] != $user['id'] && ($user['role'] ?? '') !== 'admin')) {
            $this->flash('error', 'Bạn không có quyền chỉnh sửa sản phẩm này.');
            $this->redirect('/');
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $originalPrice = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
        $condition = $_POST['condition'] ?? 'like_new';
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? $product['status'];

        if ($title === '' || strlen($title) > 255 || $categoryId <= 0 || $price <= 0
            || ($originalPrice !== null && $originalPrice <= 0)
            || !in_array($condition, ['new', 'like_new', 'good', 'fair'], true)
            || $location === '' || strlen($location) > 150 || $description === '') {
            $this->flash('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc.');
            $this->redirect('/products/edit/' . $id);
        }

        $allowedStatuses = ['available', 'hidden'];
        if (!in_array($status, $allowedStatuses, true) && $status !== $product['status']) {
            $this->flash('error', 'Trạng thái sản phẩm không hợp lệ.');
            $this->redirect('/products/edit/' . $id);
        }

        if (in_array($product['status'], ['reserved', 'sold'], true) && $status !== $product['status']) {
            $this->flash('error', 'Không thể thay đổi trạng thái sản phẩm đang giao dịch hoặc đã bán.');
            $this->redirect('/products/edit/' . $id);
        }

        $updateData = [
            'category_id'    => $categoryId,
            'title'          => $title,
            'description'    => $description,
            'price'          => $price,
            'original_price' => $originalPrice,
            'condition'      => $condition,
            'location'       => $location,
            'status'         => $status
        ];

        if (!empty($_FILES['image']['name'])) {
            try {
                $updateData['image'] = $this->uploadProductImage($_FILES['image']);
            } catch (DomainException $exception) {
                $this->flash('error', $exception->getMessage());
                $this->redirect('/products/edit/' . $id);
            }
        }

        $this->productModel->update($id, $updateData);
        $this->flash('success', 'Cập nhật sản phẩm thành công!');
        $this->redirect('/products/detail/' . $id);
    }

    /**
     * Xóa sản phẩm đăng bán
     */
    public function delete(int|string $id): void {
        $user = $this->user();
        $id = (int)$id;
        $product = $this->productModel->find($id);

        if (!$product || ($product['user_id'] != $user['id'] && ($user['role'] ?? '') !== 'admin')) {
            $this->flash('error', 'Bạn không có quyền xóa sản phẩm này.');
            $this->redirect('/');
        }

        if (!in_array($product['status'], ['available', 'hidden'], true)) {
            $this->flash('error', 'Không thể xóa sản phẩm đang giao dịch hoặc đã bán.');
            $this->redirect('/products/detail/' . $id);
        }

        $this->productModel->update($id, ['status' => 'hidden']);
        $this->flash('success', 'Đã ẩn bài đăng bán. Lịch sử giao dịch được giữ nguyên.');
        $this->redirect('/profile/seller/' . $user['id']);
    }

    private function uploadProductImage(array $file): string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new DomainException('Vui lòng chọn ảnh sản phẩm hợp lệ để tải lên.');
        }

        $maxFileSize = (int)env('UPLOAD_MAX_FILESIZE', 5242880);
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])
            || (int)($file['size'] ?? 0) <= 0 || (int)$file['size'] > $maxFileSize) {
            throw new DomainException('Ảnh không hợp lệ hoặc vượt quá dung lượng cho phép.');
        }

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];
        if (!isset($extensions[$mimeType]) || !getimagesize($file['tmp_name'])) {
            throw new DomainException('Ảnh phải có định dạng JPG, PNG hoặc WEBP.');
        }

        $uploadDir = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'products';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            throw new DomainException('Không thể tạo thư mục lưu ảnh sản phẩm.');
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . DIRECTORY_SEPARATOR . $fileName)) {
            throw new DomainException('Không thể lưu ảnh sản phẩm. Vui lòng thử lại.');
        }

        return UPLOAD_URL . '/products/' . $fileName;
    }
}
