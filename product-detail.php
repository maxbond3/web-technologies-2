<?php
require_once 'includes/Product.php';
require_once 'includes/Review.php';

$productObj = new Product();
$reviewObj = new Review();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productObj->getById($productId);

if (!$product) {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>Товар не найден</h1>';
    exit;
}

// Обработка отправки отзыва
$message = '';
$error = '';

// Обработка отправки отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $author = trim($_POST['author'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    $errors = [];

    if (empty($author)) {
        $errors[] = 'Введите ваше имя';
    }

    if (empty($email)) {
        $errors[] = 'Введите email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email';
    }

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Поставьте оценку';
    }

    if (empty($comment)) {
        $errors[] = 'Напишите отзыв';
    } elseif (strlen($comment) < 10) {
        $errors[] = 'Отзыв должен содержать минимум 10 символов';
    }

    if (empty($errors)) {
        $reviewData = [
            'product_id' => $productId,
            'author' => $author,
            'email' => $email,
            'rating' => $rating,
            'comment' => $comment
        ];

        if ($reviewObj->create($reviewData)) {
            $message = 'Спасибо! Ваш отзыв успешно отправлен!';
            // Очищаем POST после успеха
            unset($_POST['author'], $_POST['email'], $_POST['rating'], $_POST['comment']);
        } else {
            $error = 'Ошибка при сохранении отзыва. Попробуйте позже.';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

$reviews = $reviewObj->getByProductId($productId);
$rating = $reviewObj->getAverageRating($productId);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Карточка товара</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1><a href="index.php">Каталог товаров</a></h1>
            </div>
            <nav class="breadcrumbs">
                <a href="index.php">Главная</a> /
                <?php if ($product['category_name']): ?>
                    <a href="index.php?category=<?php echo $product['category_id']; ?>">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </a> /
                <?php endif; ?>
                <span><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>
        </header>

        <div class="product-detail">
            <div class="product-gallery">
                <img src="<?php echo UPLOAD_DIR . $product['image']; ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>

            <div class="product-detail-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="product-rating">
                    <?php echo generateStars($rating['avg_rating']); ?>
                    <span class="rating-value">
                        <?php echo number_format($rating['avg_rating'], 1); ?>
                    </span>
                    <span class="reviews-count">
                        (<?php echo $rating['total']; ?> отзывов)
                    </span>
                </div>

                <div class="product-price-detail">
                    <?php echo number_format($product['price'], 2, '.', ' '); ?> ₽
                </div>

                <div class="product-description-full">
                    <h3>Описание:</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <button class="add-to-cart-large" data-id="<?php echo $product['id']; ?>">
                    Добавить в корзину
                </button>
            </div>
        </div>

        <!-- Секция отзывов -->
        <div class="reviews-section">
            <h2>Отзывы (<?php echo $rating['total']; ?>)</h2>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Форма добавления отзыва -->
            <div class="review-form">
                <h3>Оставить отзыв</h3>
                <form method="POST" action="" id="reviewForm">
                    <div class="form-group">
                        <label for="author">Ваше имя *</label>
                        <input type="text" id="author" name="author"
                            value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Оценка *</label>
                        <div class="rating-input" id="ratingStars">
                            <span class="star" data-value="5">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="1">★</span>
                        </div>
                        <input type="hidden" name="rating" id="ratingValue"
                            value="<?php echo isset($_POST['rating']) ? (int)$_POST['rating'] : '0'; ?>">
                        <div class="rating-error">Пожалуйста, выберите оценку</div>
                    </div>

                    <div class="form-group">
                        <label for="comment">Ваш отзыв *</label>
                        <textarea id="comment" name="comment" rows="4" required minlength="10"><?php
                                                                                                echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : '';
                                                                                                ?></textarea>
                    </div>

                    <button type="submit" name="submit_review" class="btn-submit">
                        Отправить отзыв
                    </button>
                </form>
            </div>

            <!-- Список отзывов -->
            <div class="reviews-list">
                <?php if (empty($reviews)): ?>
                    <p class="no-reviews">Пока нет отзывов. Будьте первым!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <strong><?php echo htmlspecialchars($review['author']); ?></strong>
                                <span class="review-date">
                                    <?php echo date('d.m.Y', strtotime($review['created_at'])); ?>
                                </span>
                            </div>

                            <div class="review-rating">
                                <?php echo generateStars($review['rating']); ?>
                            </div>

                            <div class="review-comment">
                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>

</html>

<?php
function generateStars($rating)
{
    $stars = '';
    $rating = round($rating);

    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '★';
        } else {
            $stars .= '☆';
        }
    }

    return $stars;
}
?>