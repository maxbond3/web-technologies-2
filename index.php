<?php
require_once 'MenuBuilder.php';

$menu = new MenuBuilder();
$menuHtml = $menu->getFullMenu();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог товаров - Многоуровневое меню</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <!-- Шапка -->
        <header class="header">
            <div class="logo">
                <img src="logo.png" alt="Логотип" class="logo-img">
                <span class="logo-text">Каталог товаров</span>
            </div>
            <div class="header-contacts">
                <span class="phone">8 (800) 123-45-67</span>
                <span class="email">info@catalog.ru</span>
            </div>
        </header>

        <!-- Боковое меню -->
        <aside class="sidebar">
            <h2 class="sidebar-title">Каталог</h2>
            <nav class="main-nav">
                <?php echo $menuHtml; ?>
            </nav>
        </aside>

        <!-- Основной контент -->
        <main class="content">
            <h1>Добро пожаловать в каталог товаров</h1>
            <p>Выберите категорию в меню слева для просмотра товаров.</p>
        </main>
    </div>

    <script src="menu.js"></script>
</body>

</html>