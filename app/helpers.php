<?php
/**
 * Created by PhpStorm.
 * User: CLF
 * Date: 2020/9/18
 * Time: 14:01
 */
    function route_class()
    {
        return str_replace('.', '-', Route::currentRouteName());
    }