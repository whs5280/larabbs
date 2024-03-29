<?php

use Illuminate\Support\Str;

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function category_nav_active($category_id)
{
    return active_class((if_route('categories.show')) && if_route_param('category', $category_id));
}

function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return \Illuminate\Support\Str::limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    // 获取数据模型的复数蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点 URL 拼接全量 URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // 拼接 HTML A 标签，并返回
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // 从实体中获取完整类名，例如：App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
    $class_name = class_basename($full_class_name);

    // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name = Str::snake($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return Str::plural($snake_case_name);
}

/**
 * XSS 安全过滤
 */
if (!function_exists('xss_safe_filter')) {
    function xss_safe_filter($value)
    {
        if (empty($value)) {
            return $value;
        } else {
            return is_array($value) ? array_map('xss_safe_filter', $value) : get_htmlspecialchars($value);
        }
    }
}

/**
 * 获取htmlspecialchars
 * @param $value
 * @return string
 */
if (!function_exists('get_htmlspecialchars'))
{
    function get_htmlspecialchars($value): string
    {
        $no = '/%0[0-8bcef]/';
        $value = preg_replace($no,' ', $value);

        $no = '/%1[0-9a-f]/';
        $value = preg_replace($no,' ', $value);

        $no = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
        $value = preg_replace($no, ' ', $value);

        return htmlspecialchars($value, ENT_QUOTES);
    }
}
