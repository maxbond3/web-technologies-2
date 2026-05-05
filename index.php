<?php

$title = "Мой первый PHP сайт";
$h1 = "Добро пожаловать в мир PHP!";
$currentYear = date("Y");
$time = getSkilledTime();

function getSkilledTime()
{
    $hours = (int)date('H');
    $minutes = (int)date('i');

    function declension($number, $titles)
    {
        $lastTwoDigits = $number % 100;
        $lastDigit = $number % 10;

        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 14) {
            return $titles[2];
        }

        if ($lastDigit == 1) {
            return $titles[0];
        }

        if ($lastDigit >= 2 && $lastDigit <= 4) {
            return $titles[1];
        }

        return $titles[2];
    }

    $h_text = declension($hours, ['час', 'часа', 'часов']);
    $m_text = declension($minutes, ['минута', 'минуты', 'минут']);

    return "$hours $h_text $minutes $m_text";
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
</head>

<body>
    <h1><?= $h1; ?></h1>
    <p>Текущий год: <?= $currentYear; ?></p>
    <p>Текущая время: <?= $time; ?></p>
</body>

</html>