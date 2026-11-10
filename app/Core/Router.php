<?php
/**
 * ========================================================
 * ROUTER - ĐIỀU HƯỚNG URL TRONG HỆ THỐNG MVC
 * ========================================================
 */

namespace App\Core;

class Router {
    protected static array $routes = [];

    /**
     * Đăng ký route GET
     */
    public static function get(string $path, array|string $action, array $middlewares = []): void {
        self::addRoute('GET', $path, $action, $middlewares);
    }

    /**
     * Đăng ký route POST
     */
    public static function post(string $path, array|string $action, array $middlewares = []): void {
        self::addRoute('POST', $path, $action, $middlewares);
    }

    /**
     * Thêm route vào danh sách
     */
    protected static function addRoute(string $method, string $path, array|string $action, array $middlewares): void {
        $path = '/' . trim($path, '/');
        self::$routes[] = [
            'method'      => $method,
            'path'        => $path,
            'action'      => $action,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Điều hướng request hiện tại tới Controller tương ứng
     */
    public static function dispatch(string $uri, string $method): void {
        // Tách query string ra khỏi URI
        $uriParts = explode('?', $uri);
        $requestPath = '/' . trim($uriParts[0], '/');
        $requestMethod = strtoupper($method);

        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Chuyển {param} thành regex pattern
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestPath, $matches)) {
                // Lọc các param dạng chuỗi
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Chạy Middlewares nếu có
                foreach ($route['middlewares'] as $middleware) {
                    if (class_exists($middleware)) {
                        $mwInstance = new $middleware();
                        if (method_exists($mwInstance, 'handle')) {
                            $mwInstance->handle();
                        }
                    }
                }

                // Thực thi Controller Action
                self::executeAction($route['action'], $params);
                return;
            }
        }

        // Không tìm thấy route -> 404
        http_response_code(404);
        require_once APP_PATH . '/Views/layouts/header.php';
        echo "<div class='container my-5 text-center'>
                <h1 class='display-1 text-danger fw-bold'>404</h1>
                <h2 class='mb-3'>Trang không tồn tại</h2>
                <p class='text-muted'>Đường dẫn bạn yêu cầu không có trên hệ thống.</p>
                <a href='" . BASE_URL . "/' class='btn btn-primary mt-2'>Quay về Trang chủ</a>
              </div>";
        require_once APP_PATH . '/Views/layouts/footer.php';
    }

    /**
     * Gọi hàm Controller hoặc Closure
     */
    protected static function executeAction(array|string $action, array $params = []): void {
        if (is_array($action)) {
            [$controllerClass, $method] = $action;
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        } elseif (is_string($action) && str_contains($action, '@')) {
            [$controllerName, $method] = explode('@', $action);
            $fullClass = "App\\Controllers\\{$controllerName}";
            if (class_exists($fullClass)) {
                $controller = new $fullClass();
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }

        die("Lỗi: Không tìm thấy Controller hoặc Phương thức xử lý.");
    }
}

