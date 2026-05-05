<?php

// Задача 1
$a = 5;
$b = -2;
$result = 0;

if ($a >= 0 && $b >= 0) {
    $result = $a - $b;
} elseif ($a < 0 && $b < 0) {
    $result = $a * $b;
} else {
    $result = $a + $b;
}

echo <<<EOF
<b>Задача 1</b><br>
a=$a<br>
b=$b<br>
Результат: $result
EOF;

echo "<br>" . str_repeat("-", 20) . "<br>";


// Задача 2
$a = 11;

echo <<<EOF
<b>Задача 2</b><br>
a=$a<br>
Результат: 
EOF;

switch ($a) {
    case 11:
        echo "11 ";
    case 12:
        echo "12 ";
    case 13:
        echo "13 ";
    case 14:
        echo "14 ";
    case 15:
        echo "15";
}

echo "<br>" . str_repeat("-", 20) . "<br>";


// Задача 3
function add($a, $b)
{
    return $a + $b;
}
function sub($a, $b)
{
    return $a - $b;
}
function mult($a, $b)
{
    return $a * $b;
}
function div($a, $b)
{
    return $b != 0 ? $a / $b : "Ошибка: деление на ноль";
}


// Задача 4
function mathOperation($arg1, $arg2, $operation)
{
    switch ($operation) {
        case 'add':
            return add($arg1, $arg2);
        case 'sub':
            return sub($arg1, $arg2);
        case 'mul':
            return mult($arg1, $arg2);
        case 'div':
            return div($arg1, $arg2);
        default:
            return "Операция не найдена";
    }
}


// Задача 5
echo "<b>Задача 5</b><br>";

echo "Способ 1: Функция date()<br>";
echo date("Y");

echo "<br>Способ 2: Функция getdate()<br>";
$date = getdate();
echo $date['year'];

echo "<br>Способ 3: Класс DateTime<br>";
$now = new DateTime();
echo $now->format("Y");


// Задача 6
function power($val, $pow)
{
    if ($pow == 0) return 1;
    if ($pow < 0) return 1 / power($val, abs($pow));
    return $val * power($val, $pow - 1);
}
