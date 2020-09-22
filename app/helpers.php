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

    function category_nav_active($category_id)
    {
        return active_class((if_route('categories.show')) && if_route_param('category', $category_id));
    }