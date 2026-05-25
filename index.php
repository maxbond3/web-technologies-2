<?php
// ============================================
// Задание 4*. Логирование запросов
// ============================================
function logRequest()
{
    $logFile = 'log.txt';
    $maxLines = 10;

    // Добавляем новую запись
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] Запрос к index.php\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);

    // Задание 5*. Проверяем количество строк и архивируем при необходимости
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);

        if (count($lines) > $maxLines) {
            // Находим следующий номер архива
            $archiveNumber = 1;
            while (file_exists("log{$archiveNumber}.txt")) {
                $archiveNumber++;
            }

            // Перемещаем старые записи в архив (кроме последней, которая только что добавлена)
            $oldEntries = array_slice($lines, 0, -1);
            file_put_contents("log{$archiveNumber}.txt", implode("\n", $oldEntries) . "\n");

            // Оставляем только последнюю запись в основном файле
            file_put_contents($logFile, end($lines) . "\n");
        }
    }
}

// Вызываем логирование при каждом запросе
logRequest();

// ============================================
// Настройки для загрузки файлов
// ============================================
$uploadDir = 'uploads/';
$thumbDir = 'uploads/thumbnails/';
$maxFileSize = 5 * 1024 * 1024; // 5 MB
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Создаем директории, если их нет
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_dir($thumbDir)) {
    mkdir($thumbDir, 0777, true);
}

// ============================================
// Задание 3. Обработка загрузки изображения
// ============================================
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Проверка на ошибки загрузки
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Проверка типа файла
        $fileType = mime_content_type($file['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            // Проверка размера файла
            if ($file['size'] <= $maxFileSize) {
                // Генерируем уникальное имя файла
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = uniqid() . '.' . $extension;
                $uploadPath = $uploadDir . $newFilename;
                $thumbPath = $thumbDir . $newFilename;

                // Перемещаем загруженный файл
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Создаем миниатюру
                    createThumbnail($uploadPath, $thumbPath, 200, 150);
                    $message = "Изображение успешно загружено!";

                    // Перезагружаем страницу
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $error = "Ошибка при сохранении файла.";
                }
            } else {
                $error = "Файл слишком большой. Максимальный размер: " . ($maxFileSize / 1024 / 1024) . " MB.";
            }
        } else {
            $error = "Недопустимый тип файла. Разрешены только: JPG, PNG, GIF, WebP.";
        }
    } else {
        $error = "Ошибка при загрузке файла. Код ошибки: " . $file['error'];
    }
}

// ============================================
// Функция создания миниатюры
// ============================================
function createThumbnail($sourcePath, $thumbPath, $thumbWidth, $thumbHeight)
{
    // Получаем информацию о исходном изображении
    list($width, $height, $type) = getimagesize($sourcePath);

    // Создаем исходное изображение в зависимости от типа
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    // Вычисляем пропорции
    $ratio = min($thumbWidth / $width, $thumbHeight / $height);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);

    // Создаем новое изображение
    $thumb = imagecreatetruecolor($newWidth, $newHeight);

    // Для PNG сохраняем прозрачность
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    // Изменяем размер
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Сохраняем миниатюру
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $thumbPath, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $thumbPath, 8);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $thumbPath);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($thumb, $thumbPath, 85);
            break;
    }

    // Освобождаем память
    imagedestroy($source);
    imagedestroy($thumb);

    return true;
}

// ============================================
// Задание 1 и 2. Функция построения галереи
// ============================================
function buildGallery($directory, $thumbDirectory)
{
    $images = [];

    // Получаем список файлов из директории
    $files = scandir($directory);

    foreach ($files as $file) {
        // Пропускаем системные файлы и директории
        if ($file == '.' || $file == '..' || is_dir($directory . $file)) {
            continue;
        }

        // Проверяем, является ли файл изображением
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($extension, $imageExtensions)) {
            $thumbnailPath = $thumbDirectory . $file;

            // Если миниатюра не существует, используем оригинал
            if (!file_exists($thumbnailPath)) {
                $thumbnailPath = $directory . $file;
            }

            $images[] = [
                'original' => $directory . $file,
                'thumbnail' => $thumbnailPath,
                'name' => $file
            ];
        }
    }

    return $images;
}

// Получаем список изображений
$images = buildGallery($uploadDir, $thumbDir);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фотогалерея</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .gallery-item .image-name {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px;
            font-size: 12px;
            text-align: center;
        }

        .empty-gallery {
            text-align: center;
            padding: 50px;
            color: #999;
            font-size: 1.2em;
        }

        .upload-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .upload-form h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .form-group input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            background: white;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s;
        }

        .btn-submit:hover {
            transform: scale(1.05);
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info {
            text-align: center;
            color: #666;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📸 Фотогалерея</h1>

        <!-- Сообщения об ошибках или успехе -->
        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Форма загрузки изображения -->
        <div class="upload-form">
            <h2>Загрузить новое изображение</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="image">Выберите изображение:</label>
                    <input type="file" name="image" id="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn-submit">Загрузить</button>
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    Разрешённые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5 MB
                </p>
            </form>
        </div>

        <!-- Галерея изображений -->
        <h2 style="color: #333; margin-bottom: 20px;">Мои изображения (<?php echo count($images); ?>)</h2>

        <?php if (count($images) > 0): ?>
            <div class="gallery">
                <?php foreach ($images as $image): ?>
                    <a href="<?php echo $image['original']; ?>" target="_blank" class="gallery-item">
                        <img src="<?php echo $image['thumbnail']; ?>" alt="<?php echo $image['name']; ?>">
                        <div class="image-name"><?php echo $image['name']; ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-gallery">
                <p>Галерея пуста. Загрузите первое изображение!</p>
            </div>
        <?php endif; ?>

        <div class="info">
            <p>Всего загружено изображений: <?php echo count($images); ?></p>
            <p>Нажмите на изображение, чтобы открыть его в новой вкладке в полном размере.</p>
        </div>
    </div>
</body>

</html>