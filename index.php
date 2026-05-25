<?php
// Задание 1. Вывод чисел от 0 до 10 с помощью do...while
echo "<h2>Задание 1. Числа от 0 до 10</h2>";
function printNumbers()
{
    $i = 0;
    do {
        if ($i == 0) {
            echo "$i – это ноль.<br>";
        } elseif ($i % 2 == 0) {
            echo "$i – чётное число.<br>";
        } else {
            echo "$i – нечётное число.<br>";
        }
        $i++;
    } while ($i <= 10);
}
printNumbers();

// Задание 2. Массив областей и городов
echo "<h2>Задание 2. Области и города</h2>";
$regions = [
    'Московская область' => ['Москва', 'Зеленоград', 'Клин'],
    'Ленинградская область' => ['Санкт-Петербург', 'Всеволожск', 'Павловск', 'Кронштадт'],
    'Рязанская область' => ['Рязань', 'Касимов', 'Скопин', 'Сасово'],
    'Тверская область' => ['Тверь', 'Ржев', 'Вышний Волочёк', 'Кимры', 'Торжок'],
    'Калужская область' => ['Калуга', 'Обнинск', 'Козельск', 'Малоярославец']
];

foreach ($regions as $region => $cities) {
    echo "<strong>$region:</strong><br>";
    echo implode(', ', $cities) . ".<br><br>";
}

// Задание 2*. Только города на букву "К"
echo "<h2>Задание 2*. Города на букву 'К'</h2>";
foreach ($regions as $region => $cities) {
    $filteredCities = array_filter($cities, function ($city) {
        return mb_substr($city, 0, 1) === 'К';
    });

    if (!empty($filteredCities)) {
        echo "<strong>$region:</strong><br>";
        echo implode(', ', $filteredCities) . ".<br><br>";
    }
}

// Задание 3. Транслитерация строк
echo "<h2>Задание 3. Транслитерация</h2>";

function transliterate($string)
{
    $translitMap = [
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'kh',
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'shch',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'Y',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'Kh',
        'Ц' => 'Ts',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Shch',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya'
    ];

    return strtr($string, $translitMap);
}

$text = "Привет, мир! Как дела?";
echo "Исходный текст: $text<br>";
echo "Транслитерация: " . transliterate($text) . "<br>";

// Задание 4. Динамическое меню
echo "<h2>Задание 4. Динамическое меню</h2>";

function generateMultilevelMenu($menuItems)
{
    $html = '<ul>';
    foreach ($menuItems as $item) {
        $html .= '<li>';
        $html .= '<a href="' . $item['url'] . '">' . $item['title'] . '</a>';

        if (isset($item['children']) && !empty($item['children'])) {
            $html .= generateMultilevelMenu($item['children']);
        }

        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}

$menu = [
    [
        'title' => 'Главная',
        'url' => '/'
    ],
    [
        'title' => 'О компании',
        'url' => '/about',
        'children' => [
            ['title' => 'История', 'url' => '/about/history'],
            ['title' => 'Команда', 'url' => '/about/team'],
            ['title' => 'Вакансии', 'url' => '/about/jobs']
        ]
    ],
    [
        'title' => 'Услуги',
        'url' => '/services',
        'children' => [
            ['title' => 'Веб-разработка', 'url' => '/services/web'],
            [
                'title' => 'Дизайн',
                'url' => '/services/design',
                'children' => [
                    ['title' => 'Графический дизайн', 'url' => '/services/design/graphic'],
                    ['title' => 'UI/UX дизайн', 'url' => '/services/design/ui-ux']
                ]
            ],
            ['title' => 'SEO продвижение', 'url' => '/services/seo']
        ]
    ],
    [
        'title' => 'Контакты',
        'url' => '/contacts'
    ]
];

echo "<h3>Простое меню:</h3>";
echo generateMultilevelMenu(array_slice($menu, 0, 2)); // Показываем первые 2 пункта для примера

echo "<h3>Многоуровневое меню:</h3>";
echo generateMultilevelMenu($menu);

// Дополнительно: стили для меню
?>
<style>
    ul {
        list-style: none;
        padding-left: 20px;
    }

    li {
        margin: 5px 0;
    }

    a {
        text-decoration: none;
        color: #333;
    }

    a:hover {
        color: #007bff;
    }

    ul ul {
        border-left: 2px solid #ddd;
        margin-left: 10px;
    }

    h2 {
        border-bottom: 2px solid #333;
        padding-bottom: 5px;
        margin-top: 30px;
    }
</style>