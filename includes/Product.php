<?php
require_once 'config.php';

class Product
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDBConnection();
    }

    /**
     * Получение всех товаров с пагинацией
     */
    public function getAll($page = 1, $perPage = 12, $categoryId = null)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";

        $params = [];

        if ($categoryId) {
            // Получаем все ID подкатегорий
            $categoryIds = $this->getAllSubcategoryIds($categoryId);
            $categoryIds[] = $categoryId; // Добавляем саму категорию

            // Создаем плейсхолдеры для IN
            $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
            $sql .= " WHERE p.category_id IN ($placeholders)";
            $params = $categoryIds;
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Получение всех ID подкатегорий (рекурсивно)
     */
    private function getAllSubcategoryIds($parentId)
    {
        $ids = [];
        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE parent_id = ?");
        $stmt->execute([$parentId]);
        $subcategories = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($subcategories as $subId) {
            $ids[] = $subId;
            // Рекурсивно получаем подкатегории подкатегорий
            $childIds = $this->getAllSubcategoryIds($subId);
            $ids = array_merge($ids, $childIds);
        }

        return $ids;
    }

    /**
     * Получение общего количества товаров
     */
    public function getTotal($categoryId = null)
    {
        $sql = "SELECT COUNT(*) FROM products p";
        $params = [];

        if ($categoryId) {
            $categoryIds = $this->getAllSubcategoryIds($categoryId);
            $categoryIds[] = $categoryId;

            $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
            $sql .= " WHERE p.category_id IN ($placeholders)";
            $params = $categoryIds;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }

    /**
     * Получение товара по ID
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Создание товара
     */
    public function create($data, $image = null)
    {
        $imageName = 'default.jpg';

        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->uploadImage($image);
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, price, description, image, category_id) 
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['description'],
            $imageName,
            $data['category_id'] ?? null
        ]);
    }

    /**
     * Обновление товара
     */
    public function update($id, $data, $image = null)
    {
        $product = $this->getById($id);
        $imageName = $product['image'];

        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            if ($imageName !== 'default.jpg') {
                $this->deleteImage($imageName);
            }
            $imageName = $this->uploadImage($image);
        }

        $stmt = $this->pdo->prepare("
            UPDATE products 
            SET name = ?, price = ?, description = ?, image = ?, category_id = ? 
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['description'],
            $imageName,
            $data['category_id'] ?? null,
            $id
        ]);
    }

    /**
     * Удаление товара
     */
    public function delete($id)
    {
        $product = $this->getById($id);

        if ($product['image'] !== 'default.jpg') {
            $this->deleteImage($product['image']);
        }

        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Загрузка изображения
     */
    private function uploadImage($file)
    {
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, ALLOWED_TYPES)) {
            throw new Exception('Недопустимый тип файла');
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('Файл слишком большой');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = UPLOAD_DIR . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        throw new Exception('Ошибка загрузки файла');
    }

    /**
     * Удаление изображения
     */
    private function deleteImage($filename)
    {
        $path = UPLOAD_DIR . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Получение родительских категорий (для меню)
     */
    public function getCategories()
    {
        $stmt = $this->pdo->query("
            SELECT * FROM categories 
            WHERE parent_id IS NULL 
            ORDER BY sort_order
        ");
        return $stmt->fetchAll();
    }

    /**
     * Получение всех категорий (для дерева)
     */
    public function getAllCategories()
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY sort_order");
        $categories = $stmt->fetchAll();

        return $this->buildCategoryTree($categories);
    }

    /**
     * Построение дерева категорий
     */
    private function buildCategoryTree($categories, $parentId = null)
    {
        $tree = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $children = $this->buildCategoryTree($categories, $category['id']);
                if (!empty($children)) {
                    $category['children'] = $children;
                }
                $tree[] = $category;
            }
        }
        return $tree;
    }
}
