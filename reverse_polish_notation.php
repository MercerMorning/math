<?php
$expression = '1 2+3 4++5+4*';
$pattern = '%(\d+)\s(\d+)([\+, \/, \*, \-]{1})%';

echo calculate($expression);

function calculate($expression)
{
    $pattern = '%(\d+)\s(\d+)([\+, \/, \*, \-]{1})%';
    $count = preg_match_all($pattern, $expression);

    while ($count !== 0) {
        $iteration = 1;
        $expression = preg_replace_callback($pattern, function ($symbols) use (&$iteration) {
            $result = eval('return ' . $symbols[1] . $symbols[3] . $symbols[2] . ';');

            if ($iteration % 2 !== 0) {
                $result .= ' ';
            }
            $iteration++;
            return $result;
        }, $expression);
        $count = preg_match_all($pattern, $expression);
    }

    return (float) $expression;
}
