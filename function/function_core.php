<?php
function dd($params)
{
    if (is_array($params)) {
        echo "<pre>";
        print_r($params);
        echo "</pre>";
    } else {
        echo $params . "<br />";
    }
}