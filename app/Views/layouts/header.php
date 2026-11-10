<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Secondhand Marketplace') ?> - Sàn Đồ Cũ Sinh Viên</title>
    
    <!-- Bootstrap 5 CSS & Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS chung toàn hệ thống -->
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/global.css">

    <!-- CSS riêng theo từng module -->
    <?php if (!empty($customCss)): ?>
        <?php foreach ((array)$customCss as $css): ?>
            <link rel="stylesheet" href="<?= ASSET_URL ?>/css/<?= htmlspecialchars($css) ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

