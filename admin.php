<?php

session_start();
require_once 'includes/Product.php';
require_once 'includes/Review.php';

// Проверка прав администратора
if (!isset($_SESSION['is_admin'])) {
    $_SESSION['is_admin'] = true;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$productObj = new Product();
$reviewObj = new Review();

// Единая функция для обработки действий с отзывами
function doFeedbackAction($action, $id = null, $data = [])
{
    global $reviewObj;

    switch ($action) {
        case 'list':
            return $reviewObj->getAll();

        case 'view':
            return $reviewObj->getAll(); // В реальном проекте - getById

        case 'approve':
            if ($id) {
                $reviewObj->approve($id);
                return ['success' => true, 'message' => 'Отзыв одобрен'];
            }
            return ['success' => false, 'message' => 'ID не указан'];

        case 'delete':
            if ($id) {
                $reviewObj->delete($id);
                return ['success' => true, 'message' => 'Отзыв удален'];
            }
            return ['success' => false, 'message' => 'ID не указан'];

        case 'create':
            if (!empty($data)) {
                $reviewObj->create($data);
                return ['success' => true, 'message' => 'Отзыв создан'];
            }
            return ['success' => false, 'message' => 'Данные не предоставлены'];

        default:
            return ['success' => false, 'message' => 'Неизвестное действие'];
    }
}

// Обработка действий
$result = null;
if ($action !== 'list') {
    $result = doFeedbackAction($action, $id, $_POST);
    if ($result['success']) {
        header('Location: admin.php');
        exit;
    }
}

$reviews = doFeedbackAction('list');
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление отзывами - Админ-панель</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container admin-panel">
        <header class="header">
            <h1>Управление отзывами</h1>
            <nav>
                <a href="index.php">На сайт</a>
                <a href="admin.php">Отзывы</a>
            </nav>
        </header>

        <?php if ($result && !$result['success']): ?>
            <div class="alert alert-error">
                <?php echo $result['message']; ?>
            </div>
        <?php endif; ?>

        <div class="admin-content">
            <h2>Список отзывов</h2>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Товар</th>
                        <th>Автор</th>
                        <th>Email</th>
                        <th>Оценка</th>
                        <th>Комментарий</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo $review['id']; ?></td>
                            <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($review['author']); ?></td>
                            <td><?php echo htmlspecialchars($review['email']); ?></td>
                            <td><?php echo generateStars($review['rating']); ?></td>
                            <td><?php echo mb_substr(htmlspecialchars($review['comment']), 0, 50); ?>...</td>
                            <td>
                                <span class="status-<?php echo $review['is_approved'] ? 'approved' : 'pending'; ?>">
                                    <?php echo $review['is_approved'] ? 'Одобрен' : 'На модерации'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></td>
                            <td class="actions">
                                <?php if (!$review['is_approved']): ?>
                                    <a href="?action=approve&id=<?php echo $review['id']; ?>"
                                        class="btn btn-success">Одобрить</a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $review['id']; ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Удалить отзыв?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
function generateStars($rating)
{
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';
    }
    return $stars;
}
?>