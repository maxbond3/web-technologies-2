<?php
require_once 'includes/Product.php';
require_once 'includes/Review.php';

$product = new Product();
$review = new Review();

// Параметры пагинации и фильтрации
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Получение товаров
$products = $product->getAll($page, 12, $categoryId);
$totalProducts = $product->getTotal($categoryId);
$totalPages = ceil($totalProducts / 12);

// Получение категорий
$categories = $product->getCategories();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <!-- Шапка -->
        <header class="header">
            <div class="logo">
                <h1>Каталог товаров</h1>
            </div>
            <nav class="main-nav">
                <a href="index.php">Главная</a>
                <a href="index.php?category=1">Электроника</a>
                <a href="index.php?category=2">Одежда</a>
                <a href="index.php?category=3">Дом и сад</a>
                <?php if (isset($_SESSION['is_admin'])): ?>
                    <a href="admin.php">Админ-панель</a>
                <?php endif; ?>
            </nav>
        </header>

        <div class="main-content">
            <!-- Боковая панель с категориями -->
            <aside class="sidebar">
                <h3>Категории</h3>
                <ul class="category-list">
                    <li>
                        <a href="index.php" <?php echo !$categoryId ? 'class="active"' : ''; ?>>
                            Все товары
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="index.php?category=<?php echo $cat['id']; ?>"
                                <?php echo $categoryId == $cat['id'] ? 'class="active"' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <!-- Сетка товаров -->
            <main class="products-grid">
                <?php if (empty($products)): ?>
                    <div class="empty-message">
                        <p>Товары не найдены</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $item): ?>
                        <div class="product-card">
                            <a href="product-detail.php?id=<?php echo $item['id']; ?>">
                                <div class="product-image">
                                    <img src="<?php echo UPLOAD_DIR . $item['image']; ?>"
                                        alt="<?php echo htmlspecialchars($item['name']); ?>">
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </h3>
                                    <p class="product-category">
                                        <?php echo htmlspecialchars($item['category_name'] ?? 'Без категории'); ?>
                                    </p>
                                    <div class="product-price">
                                        <?php echo number_format($item['price'], 2, '.', ' '); ?> ₽
                                    </div>
                                    <p class="product-description">
                                        <?php echo mb_substr(htmlspecialchars($item['description']), 0, 100); ?>...
                                    </p>
                                    <?php
                                    $rating = $review->getAverageRating($item['id']);
                                    ?>
                                    <div class="product-rating">
                                        <?php echo generateStars($rating['avg_rating']); ?>
                                        <span class="reviews-count">
                                            (<?php echo $rating['total']; ?> отзывов)
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <button class="add-to-cart" data-id="<?php echo $item['id']; ?>">
                                В корзину
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </main>
        </div>

        <!-- Пагинация -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $categoryId ? '&category=' . $categoryId : ''; ?>"
                        class="<?php echo $page == $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="js/main.js"></script>
</body>

</html>

<?php
// Вспомогательная функция для генерации звезд рейтинга
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