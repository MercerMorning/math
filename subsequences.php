<?php

echo PHP_EOL;
echo table(4);

function table(int $subsequenceLength) :string
{

    $subsequencesCount = 2 ** $subsequenceLength;

    $sequencesTable = '';

    for ($currentSubsequenceNum = 1; $currentSubsequenceNum <= $subsequencesCount; $currentSubsequenceNum++) {
        for ($currentSubsequenceVariableNum = 1; $currentSubsequenceVariableNum <= $subsequenceLength; $currentSubsequenceVariableNum++) {
            $sequencesTable .= get($currentSubsequenceNum, $subsequencesCount, $currentSubsequenceVariableNum);
        }
        $sequencesTable .= PHP_EOL;
    }

    echo PHP_EOL;
    return $sequencesTable;
}


function get(int $currentSubsequenceNum, int $subsequencesCount, int $currentSubsequenceVariableNum) :int
{
    $symbol = 0;

    $partSize = $subsequencesCount / 2 ** $currentSubsequenceVariableNum;

    $currentPartThreshold = $partSize;

    while ($currentSubsequenceNum > $currentPartThreshold) {
        $symbol = (int) !$symbol;
        $currentPartThreshold += $partSize;
    }

    return $symbol;
}