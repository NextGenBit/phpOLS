<?php

use phpOLS\phpOLS;

require_once __DIR__ . '/vendor/autoload.php';


$phpOLS = new phpOLS("original", "damos.csv");

try {
    $map = $phpOLS->createContextForMapById("PQAV");
    display($map->info(), $map->build()->get());
} catch (exception $e) {
    echo "An error occurred: " . $e->getMessage();
}



function display($map, $values)
{
    if ($map->getAxis('x') !== null) {
        echo "AxisX Name: {$map->getAxis('x')->getMapName()}\n";
        $axisXvalues = $values['x'];
    }

    if ($map->getAxis('y') !== null) {
        echo "AxisY Name: {$map->getAxis('y')->getMapName()}\n";
        $axisYvalues = $values['y'];
    }


    echo "\n";

    if ($map->getAxis('x') !== null) {
        foreach ($axisXvalues as $k => $value) {
            $displayVal = (isset($value["v"])) ? $value["v"] : $value;
            if ($k == 0)
                printf("%17s |", $displayVal);
            else
                printf("%8s |", $displayVal);
        }
        echo "\n";
    }

    foreach ($values['table'] as $kr => $values) {

        if ($map->getAxis('y') !== null) {
            printf("%8s|", $axisYvalues[$kr]["v"]);
        }
        foreach ($values as $k => $value) {
            printf("%8s |", $value["v"]);
        }
        echo "\n";
    }
    echo "\n\n";
}
