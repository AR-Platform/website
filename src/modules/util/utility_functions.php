<?php

/**
 * Prints the given array in a readable way.
 *
 * @param array $arr The array to print out.
 * @return void No return value.
 */
function outputArray(array $arr)
{
    foreach ($arr as $line) {
        echo "<br> - " . $line;
    }
}