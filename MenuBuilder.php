<?php
require_once 'config.php';

class MenuBuilder
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getDBConnection();
    }

    /**
     * Получение всех пунктов меню из БД
     */
    public function getMenuItems()
    {
        $stmt = $this->pdo->query("SELECT * FROM menu_items ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    }

    /**
     * Построение дерева меню
     */
    public function buildTree(array $items, $parentId = null)
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($items, $item['id']);
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }

    /**
     * Генерация HTML меню
     */
    public function renderMenu($items, $level = 0)
    {
        if (empty($items)) {
            return '';
        }

        $class = ($level === 0) ? 'main-menu' : 'sub-menu';
        $html = "<ul class=\"$class\">\n";

        foreach ($items as $item) {
            $hasChildren = isset($item['children']) && !empty($item['children']);
            $liClass = $hasChildren ? 'has-submenu' : '';

            $html .= "<li class=\"$liClass\">\n";

            // Ссылка с иконкой для разделов с подменю
            if ($hasChildren) {
                $html .= "<a href=\"{$item['url']}\" class=\"menu-link menu-toggle\">";
                $html .= "<span class=\"menu-text\">{$item['title']}</span>";
                $html .= "<span class=\"menu-arrow\">▼</span>";
                $html .= "</a>\n";
            } else {
                $html .= "<a href=\"{$item['url']}\" class=\"menu-link\">";
                $html .= "<span class=\"menu-text\">{$item['title']}</span>";
                $html .= "</a>\n";
            }

            // Рекурсивно выводим подменю
            if ($hasChildren) {
                $html .= $this->renderMenu($item['children'], $level + 1);
            }

            $html .= "</li>\n";
        }

        $html .= "</ul>\n";
        return $html;
    }

    /**
     * Получение полного меню
     */
    public function getFullMenu()
    {
        $items = $this->getMenuItems();
        $tree = $this->buildTree($items);
        return $this->renderMenu($tree);
    }
}
