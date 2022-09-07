<?php
$function = 'a&b';

$function = replaceLogicSymbols($function);

try {
    validate($function);
    $matches = [];
    $variables = array_flip(array_unique(str_split(preg_replace('/[^a-z]/i','',$function))));
    $length = count($variables);
    $subsequencesTable = table($length);

    $variableNum = 1;
    foreach ($variables as &$variable) {
        $variable = $variableNum;
        $variableNum++;
    }

    foreach ($subsequencesTable as $rowNum => $rowColumns) {
        $rowFunction = $function;
        foreach ($variables as $variableName => $variableNum) {
            $rowFunction = str_replace($variableName, $rowColumns[$variableNum], $rowFunction);
        }

        $subsequencesTable[$rowNum]['result'] = eval('return ' . $rowFunction . ';');
    }

    echo draw($subsequencesTable, $variables);
} catch (Throwable $exception) {
    echo 'Validate failed';
}

function draw(array $subsequencesTable, array $variables) :string
{
    $subsequences = '';

    $variables = array_flip($variables);

    foreach ($variables as $variableName) {
        $subsequences .= $variableName;
    }

    $subsequences .= PHP_EOL;

    foreach ($subsequencesTable as $rowNum => $rowColumns) {
        foreach ($rowColumns as $variableName => $variableValue) {
            if ($variableName === 'result') {
                $subsequences .= ' ';
            }
            $subsequences .= (int) $variableValue;
        }
        $subsequences .= PHP_EOL;
    }

    return $subsequences;
}

function table(int $subsequenceLength) :array
{
    $subsequencesCount = 2 ** $subsequenceLength;

    $sequencesTable = [];

    for ($currentSubsequenceNum = 1; $currentSubsequenceNum <= $subsequencesCount; $currentSubsequenceNum++) {
        for ($currentSubsequenceVariableNum = 1; $currentSubsequenceVariableNum <= $subsequenceLength; $currentSubsequenceVariableNum++) {
            $sequencesTable[$currentSubsequenceNum][$currentSubsequenceVariableNum] =
                get($currentSubsequenceNum, $subsequencesCount, $currentSubsequenceVariableNum);
        }
    }

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


/**
 * @throws Exception
 */
function validate(string $expression) :bool
{
    $isValidated = checkValidBrackets($expression) && checkSuspiciousConstructions($expression);
    if (!$isValidated) throw new Exception();
    return true;
}

function checkSuspiciousConstructions(string $expression) :bool
{
    $patterns = ['(\w{2,})+', '(\w{1}\W{4,}\w{1})+', '(\d)+'];
    foreach ($patterns as $pattern) {
        $result = preg_match('%' . $pattern . '%', $expression);
        if ($result) {
            return false;
        };
    }
    return true;
}

function checkValidBrackets(string $expression) :bool
{
    return (bool) preg_match('%^[^()]*+(\((?>[^()]|(?1))*+\)[^()]*+)++$%', $expression);
}

function replaceLogicSymbols(string $function) :string
{
    $function = trim($function);
    $symbols = [
      'v' => '||',
      '->' => '===='
    ];
    foreach ($symbols as $originalSymbol => $replaceSymbol) {
        $function = str_replace($originalSymbol, $replaceSymbol, $function);
    }
    return '(' . $function . ')';
}