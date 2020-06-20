<?php

namespace App\Gendiff;

function getResultOfDifference(array $result)
{
    $resultForString = [];

    foreach ($result as $item) {
        if (is_array($item)) {
            switch ($item['type']) {
                case 'unchanged':
                    $resultForString[] = sprintf('   %s: %s', $item['node'], toString($item['value']));
                    break;
                case 'changed':
                    $resultForString[] = sprintf('+  %s: %s', $item['node'], toString($item['value']));
                    $resultForString[] = sprintf('-  %s: %s', $item['node'], toString($item['prevValue']));
                    break;
                case 'removed':
                    $resultForString[] = sprintf('-  %s: %s', $item['node'], toString($item['value']));
                    break;
                case 'added':
                    $resultForString[] = sprintf('+  %s: %s', $item['node'], toString($item['value']));
                    break;
            }
        }
    }

    array_unshift($resultForString, '{');
    array_push($resultForString, '}');

    return implode(PHP_EOL, $resultForString);
}

function compareArrays($first, $second)
{
    $unique_keys = array_values(array_unique(array_merge(array_keys($first), array_keys($second))));

    return array_map(function ($child) use ($first, $second) {
        if (isset($first[$child]) && isset($second[$child]) && ($first[$child] !== $second[$child])) {
            return [
                'node'       => $child,
                'type'       => 'changed',
                'prevValue'  => $first[$child],
                'value'      => $second[$child]
            ];
        }

        if (isset($first[$child]) && isset($second[$child]) && ($first[$child] === $second[$child])) {
            return [
                'node'  => $child,
                'type'  => 'unchanged',
                'value' => $first[$child],
            ];
        }

        if (isset($first[$child]) && !isset($second[$child])) {
            return [
                'node'  => $child,
                'type'  => 'removed',
                'value' => $first[$child],
            ];
        }

        if (!isset($first[$child]) && isset($second[$child])) {
            return [
                'node'  => $child,
                'type'  => 'added',
                'value' => $second[$child],
            ];
        }
    }, $unique_keys);
}

function toString($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    return $value;
}
