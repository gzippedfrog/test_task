<?php

$numbers_array = [
    [399, 9160, 144, 3230, 407, 8875, 1597, 9835],
    [2093, 3279, 21, 9038, 918, 9238, 2592, 7467],
    [3531, 1597, 3225, 153, 9970, 2937, 8, 807],
    [7010, 662, 6005, 4181, 3, 4606, 5, 3980],
    [6367, 2098, 89, 13, 337, 9196, 9950, 5424],
    [7204, 9393, 7149, 8, 89, 6765, 8579, 55],
    [1597, 4360, 8625, 34, 4409, 8034, 2584, 2],
    [920, 3172, 2400, 2326, 3413, 4756, 6453, 8],
    [4914, 21, 4923, 4012, 7960, 2254, 4448, 1]
];

# Cумма чисел Фибоначчи в массиве
$fib_sum = 0;

# Максимальное число в массиве
$max_number = max(array_map('max', $numbers_array));

# Генерация чисел Фибоначчи до максимального числа
$fibonacci_numbers = [0, 1];

while (true) {
    $next_fib = end($fibonacci_numbers) + prev($fibonacci_numbers);
    if ($next_fib > $max_number) {
        break;
    }
    $fibonacci_numbers[] = $next_fib;
}

# Хэш таблица для быстрого поиска
$fib_set = array_flip($fibonacci_numbers);

# Подсчет суммы чисел Фибоначчи в массиве
foreach ($numbers_array as $row) {
    foreach ($row as $number) {
        if (isset($fib_set[$number])) {
            $fib_sum += $number;
        }
    }
}

echo "Сумма чисел Фибоначчи в массиве: $fib_sum\n";