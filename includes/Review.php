<?php
require_once 'config.php';

class Review
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDBConnection();
    }

    /**
     * Получение отзывов для товара
     */
    public function getByProductId($productId, $onlyApproved = true)
    {
        $sql = "SELECT * FROM reviews WHERE product_id = ?";

        if ($onlyApproved) {
            $sql .= " AND is_approved = 1";
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId]);

        return $stmt->fetchAll();
    }

    /**
     * Добавление отзыва
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reviews (product_id, author, email, rating, comment, is_approved) 
                VALUES (:product_id, :author, :email, :rating, :comment, 1)
            ");

            $result = $stmt->execute([
                ':product_id' => $data['product_id'],
                ':author' => $data['author'],
                ':email' => $data['email'],
                ':rating' => $data['rating'],
                ':comment' => $data['comment']
            ]);

            return $result;
        } catch (PDOException $e) {
            error_log("Ошибка создания отзыва: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Одобрение отзыва
     */
    public function approve($id)
    {
        $stmt = $this->pdo->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Удаление отзыва
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Получение всех отзывов (для админки)
     */
    public function getAll($onlyApproved = false)
    {
        $sql = "SELECT r.*, p.name as product_name 
                FROM reviews r 
                JOIN products p ON r.product_id = p.id";

        if ($onlyApproved) {
            $sql .= " WHERE r.is_approved = 1";
        }

        $sql .= " ORDER BY r.created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Получение среднего рейтинга товара
     */
    public function getAverageRating($productId)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COALESCE(AVG(rating), 0) as avg_rating, 
                COUNT(*) as total 
            FROM reviews 
            WHERE product_id = ? AND is_approved = 1
        ");
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }
}
